<?php



/**
 * Skeleton subclass for performing query and update operations on the 'ACTOADMIN_CONFIGURACION' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    propel.generator.lib.model
 */
class ActoadminConfiguracionPeer extends BaseActoadminConfiguracionPeer
{
    /**
     * Retorna la fila única de configuración del flujo de Actos Administrativos (UARIV-202605 CA-3.4.2).
     * Si aún no existe ninguna, retorna un objeto nuevo (no persistido) con los valores por defecto
     * (0 días = nunca depurar) para que la pantalla de administración lo pueda mostrar y guardar.
     */
    public static function getConfiguracionActual()
    {
        try {
            $c = new Criteria();
            $c->addAscendingOrderByColumn(ActoadminConfiguracionPeer::ACTOADMINCONFIGURACION_ID);
            $configuracion = ActoadminConfiguracionPeer::doSelectOne($c);
            //*********************************************************************************************
            if($configuracion == null){
                $configuracion = new ActoadminConfiguracion();
                $configuracion->setDiasRetencionBorrador(0);
            }
            //*********************************************************************************************
            return $configuracion;
        } catch (PropelException $th) {
            $configuracion = new ActoadminConfiguracion();
            $configuracion->setDiasRetencionBorrador(0);
            return $configuracion;
        }
    }
}
