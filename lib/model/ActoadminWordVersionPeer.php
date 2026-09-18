<?php

/**
 * Skeleton subclass for performing query and update operations on the 'ACTOADMIN_WORD_VERSION' table.
 *
 * Historial de versiones del archivo .docx de un Acto Administrativo (UARIV-202605, ampliación).
 * Antes de esta tabla no existía ningún rastro de versiones anteriores del Word: cada regeneración
 * pisaba ActoAdministrativo.UrlFileWord con un archivo nuevo y el anterior quedaba huérfano en disco.
 *
 * @package    propel.generator.lib.model
 */
class ActoadminWordVersionPeer extends BaseActoadminWordVersionPeer
{
    /**
     * Registra una nueva versión del archivo .docx del acto como la actual, marcando las
     * anteriores como no-actuales. Se llama cada vez que ActoAdministrativo.UrlFileWord cambia.
     */
    public static function addVersion($actoadministrativo_id, $ruta_archivo, $usuario_id)
    {
        try {
            if(empty($actoadministrativo_id) || empty(trim((string)$ruta_archivo))){ return null; }
            //*********************************************************************************************
            $c = new Criteria();
            $c->add(ActoadminWordVersionPeer::ACTOADMINISTRATIVO_ID,$actoadministrativo_id);
            $ultima = ActoadminWordVersionPeer::doSelectOne($c->addDescendingOrderByColumn(ActoadminWordVersionPeer::VERSION_NUMBER));
            //*********************************************************************************************
            $c2 = new Criteria();
            $c2->add(ActoadminWordVersionPeer::ACTOADMINISTRATIVO_ID,$actoadministrativo_id);
            $c2->add(ActoadminWordVersionPeer::CURRENT_VERSION,1);
            foreach (ActoadminWordVersionPeer::doSelect($c2) as $version_anterior) {
                $version_anterior->setCurrentVersion(0);
                $version_anterior->save();
            }
            //*********************************************************************************************
            $nueva = new ActoadminWordVersion();
            $nueva->setActoadministrativoId($actoadministrativo_id);
            $nueva->setRutaArchivo($ruta_archivo);
            $nueva->setVersionNumber($ultima ? $ultima->getVersionNumber() + 1 : 1);
            $nueva->setCurrentVersion(1);
            $nueva->setUsuarioId($usuario_id);
            $nueva->setFechaCreacion(date('Y-m-d G:i:s'));
            $nueva->save();
            //*********************************************************************************************
            return $nueva;
        } catch (PropelException $th) {
            return null;
        } catch (\Exception $th) {
            return null;
        }
    }

    public static function getListByActoId($actoadministrativo_id)
    {
        try {
            $c = new Criteria();
            $c->add(ActoadminWordVersionPeer::ACTOADMINISTRATIVO_ID,$actoadministrativo_id);
            $c->addDescendingOrderByColumn(ActoadminWordVersionPeer::VERSION_NUMBER);
            return ActoadminWordVersionPeer::doSelect($c);
        } catch (PropelException $th) {
            return array();
        } catch (\Exception $th) {
            return array();
        }
    }

    /**
     * Convierte un .docx a PDF usando LibreOffice headless, con el mismo comando ya usado por
     * ActoAdministrativo::readDocWordAndGenPdf() (lib/model/ActoAdministrativo.php), pero sin la
     * lógica de fusión de plantilla/marcas de agua propia del documento vigente: aquí se necesita
     * el PDF "tal cual" de una versión histórica ya generada, para compararla visualmente.
     */
    public static function convertirDocxAPdf($ruta_docx, $directorio_salida)
    {
        if(empty($ruta_docx) || !file_exists($ruta_docx)){ return null; }
        //*************************************************************************************************
        simad_util::createPath($directorio_salida);
        $soffice_cli = simad_util::libreOfficeCliPath();
        $comando = sprintf(
            $soffice_cli.' --headless --convert-to pdf --outdir %s %s',
            escapeshellarg($directorio_salida),
            escapeshellarg($ruta_docx)
        );
        exec($comando.' 2>&1', $output, $returnVar);
        //*************************************************************************************************
        $ruta_pdf = $directorio_salida.DIRECTORY_SEPARATOR.pathinfo($ruta_docx,PATHINFO_FILENAME).'.pdf';
        return file_exists($ruta_pdf) ? $ruta_pdf : null;
    }

    /**
     * Convierte un PDF en una imagen JPEG por página, reutilizando el mismo binario y patrón ya
     * usado en apps/recibida/modules/com_recibida/actions/actions.class.php (pdftoppm, vendorizado
     * en lib/poppler-24.07.0).
     */
    public static function convertirPdfAImagenes($ruta_pdf, $directorio_salida, $prefijo)
    {
        if(empty($ruta_pdf) || !file_exists($ruta_pdf)){ return array(); }
        //*************************************************************************************************
        simad_util::createPath($directorio_salida);
        $pdftoppm_dir = sfConfig::get('sf_lib_dir').DIRECTORY_SEPARATOR.'poppler-24.07.0'.DIRECTORY_SEPARATOR.'Library'.DIRECTORY_SEPARATOR.'bin';
        $comando = $pdftoppm_dir.DIRECTORY_SEPARATOR.'pdftoppm -scale-to 1000 -jpeg '.escapeshellarg($ruta_pdf).' '.escapeshellarg($directorio_salida.DIRECTORY_SEPARATOR.$prefijo);
        shell_exec($comando);
        //*************************************************************************************************
        $imagenes = glob($directorio_salida.DIRECTORY_SEPARATOR.$prefijo.'*.jpg');
        sort($imagenes);
        return $imagenes;
    }
}
