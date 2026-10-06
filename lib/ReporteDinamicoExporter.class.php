<?php

require_once(dirname(__FILE__) . '/Spout/Autoloader/autoload.php');

use Box\Spout\Writer\Common\Creator\WriterEntityFactory;
use Box\Spout\Writer\Common\Creator\Style\StyleBuilder;
use Box\Spout\Writer\WriterInterface;
use Box\Spout\Writer\XLSX\Writer as XLSXWriter;
use Box\Spout\Common\Entity\Style\Color;

/**
 * Construye y escribe la exportación del reporte dinámico (busqueda_avanzada)
 * a partir de los filtros guardados en sesión y los campos elegidos por el usuario.
 *
 * Las filas se leen del cursor una a una y se escriben en streaming, de modo que
 * el consumo de memoria no depende del número de registros exportados.
 */
class ReporteDinamicoExporter
{
    /** Filas de datos por hoja: el límite de XLSX es 1.048.576 incluyendo el encabezado. */
    const XLSX_MAX_DATA_ROWS = 1048575;

    /**
     * Valida la búsqueda en sesión y los campos elegidos, y arma el Criteria de exportación.
     *
     * @param array|null $fields Valores del duallistbox con formato "chk_Clase.Campo"
     * @return array ['criteria' => Criteria, 'headers' => string[], 'main_class' => string]
     * @throws InvalidArgumentException Con un mensaje apto para mostrar al usuario
     */
    public static function prepareExport($fields)
    {
        if (empty($fields) || !is_array($fields)) {
            throw new InvalidArgumentException('Debe seleccionar al menos un campo para exportar.');
        }

        if (!isset($_SESSION['rptdinamic_search']['sess_maintablename'], $_SESSION['rptdinamic_search']['sess_modulo'])) {
            throw new InvalidArgumentException('Error relacionado con la sesión del usuario, realice nuevamente la consulta.');
        }

        $search = $_SESSION['rptdinamic_search'];
        $params = isset($search['params_search']) ? unserialize(base64_decode($search['params_search'])) : array();
        if (empty($params)) {
            throw new InvalidArgumentException('Error con los filtros de consulta.');
        }

        $baseCriteria = self::buildBaseCriteria($search['sess_modulo'], $params);
        if (!$baseCriteria instanceof Criteria) {
            throw new InvalidArgumentException('No fue posible construir la consulta del reporte.');
        }

        $export = self::buildExportCriteria($baseCriteria, $search['sess_maintablename'], $fields);
        if (empty($export['headers'])) {
            throw new InvalidArgumentException('Los campos seleccionados no son válidos para el módulo consultado.');
        }

        return $export;
    }

    /**
     * Criteria con los filtros del módulo consultado; solo arma la consulta, no la ejecuta.
     *
     * @return Criteria|null
     */
    private static function buildBaseCriteria($modulo, array $params)
    {
        switch ($modulo) {
            case ModulesEnable::Archivo:
                $data = UnidadDocumentalPeer::getReporteUnidadDocumental($params, false);
                return is_array($data) ? $data['criteria'] : null;
            case ModulesEnable::ComEnviada:
                return ComEnviadaPeer::getReporteComEnviada($params);
            case ModulesEnable::ComRecibida:
                $data = ComRecibidaPeer::getReporteComRecibida($params, false);
                return is_array($data) ? $data['criteria'] : null;
            case ModulesEnable::ComInterna:
                return ComInternaPeer::getReporteComInterna($params);
        }

        return null;
    }

    /**
     * Agrega al Criteria base las columnas elegidas y los joins a las tablas relacionadas.
     */
    private static function buildExportCriteria(Criteria $baseCriteria, $mainClass, array $fields)
    {
        $mainPeer = sprintf('%sPeer', $mainClass);
        $tableMapMain = $mainPeer::getTableMap();
        $mainForeignKeys = $tableMapMain->getForeignKeys();

        $criteria = clone $baseCriteria;
        $criteria->clearSelectColumns();
        $criteria->clearOrderByColumns();
        $criteria->setLimit(0);

        $headers = array();
        foreach ($fields as $value) {
            $parts = explode('_', str_replace('.', '_', $value));
            if (count($parts) < 3) {
                continue;
            }
            $peerName = sprintf('%sPeer', $parts[1]);
            if (!class_exists($peerName)) {
                continue;
            }
            $tableMap = $peerName::getTableMap();

            // La tabla del campo se enlaza con la FK de la tabla principal cuya columna referenciada
            // existe en ella sin ser a su vez una FK (misma regla con la que se arma la lista de campos).
            $relation = null;
            foreach ($mainForeignKeys as $fk) {
                if (!$tableMap->hasColumn($fk->getRelatedColumnName())) {
                    continue;
                }
                if ($tableMap->getColumn($fk->getRelatedColumnName())->getRelatedColumnName()) {
                    continue;
                }
                $relation = $fk;
                break;
            }

            $phpNames = $peerName::getFieldNames(BasePeer::TYPE_PHPNAME);
            $colNames = $peerName::getFieldNames(BasePeer::TYPE_COLNAME);
            $index = array_search($parts[2], $phpNames, true);
            if ($index === false) {
                continue;
            }

            $alias = strtoupper(str_replace('.', '_', $colNames[$index]));
            if (in_array($alias, $headers, true)) {
                continue;
            }

            if ($relation !== null) {
                self::addJoinOnce($criteria, $tableMapMain->getName(), $relation);
            }
            $criteria->addAsColumn($alias, $colNames[$index]);
            $headers[] = $alias;
        }

        return array('criteria' => $criteria, 'headers' => $headers, 'main_class' => $mainClass);
    }

    /**
     * Une la tabla relacionada solo si el Criteria aún no la incluye: los filtros del reporte
     * pueden haberla unido ya y SQL Server rechaza dos joins con el mismo nombre expuesto.
     * Las FK que admiten nulos usan LEFT JOIN para no descartar registros sin relación.
     */
    private static function addJoinOnce(Criteria $criteria, $mainTableName, ColumnMap $fk)
    {
        $relatedTable = $fk->getRelatedTableName();
        foreach ($criteria->getJoins() as $join) {
            if (strcasecmp($join->getRightTableName(), $relatedTable) === 0) {
                return;
            }
        }

        $criteria->addJoin(
            sprintf('%s.%s', $mainTableName, $fk->getName()),
            sprintf('%s.%s', $relatedTable, $fk->getRelatedColumnName()),
            $fk->isNotNull() ? Criteria::INNER_JOIN : Criteria::LEFT_JOIN
        );
    }

    /**
     * Ejecuta la consulta y escribe encabezado + filas en el writer ya abierto.
     * En XLSX abre una hoja nueva cada vez que se alcanza el límite de filas por hoja.
     *
     * @return int Número de filas de datos escritas
     */
    public static function writeRows(WriterInterface $writer, array $export)
    {
        $isXlsx = $writer instanceof XLSXWriter;
        $headerRow = $isXlsx
            ? WriterEntityFactory::createRowFromArray($export['headers'], self::buildHeaderStyle())
            : WriterEntityFactory::createRowFromArray($export['headers']);

        $writer->addRow($headerRow);

        $mainPeer = sprintf('%sPeer', $export['main_class']);
        $stmt = $mainPeer::doSelectStmt($export['criteria']);

        $count = 0;
        $sheetRows = 0;
        while ($rowData = $stmt->fetch(PDO::FETCH_NUM)) {
            if ($isXlsx && $sheetRows === self::XLSX_MAX_DATA_ROWS) {
                $writer->addNewSheetAndMakeItCurrent();
                $writer->addRow($headerRow);
                $sheetRows = 0;
            }
            $writer->addRow(WriterEntityFactory::createRowFromArray($rowData));
            $count++;
            $sheetRows++;
        }
        $stmt->closeCursor();

        return $count;
    }

    /**
     * Estilo por defecto de las filas de datos en XLSX; se fija una sola vez en el writer
     * en lugar de por fila para no recalcularlo en cada registro.
     */
    public static function buildDataStyle()
    {
        return (new StyleBuilder())->setFontSize(10)->build();
    }

    private static function buildHeaderStyle()
    {
        return (new StyleBuilder())
            ->setFontBold()
            ->setBackgroundColor(Color::LIGHT_BLUE)
            ->build();
    }
}
