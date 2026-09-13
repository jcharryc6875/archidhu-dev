<?php

/**
 * Subclass for representing a row from the 'NOVEDADES' table.
 *
 * 
 *
 * @package lib.model
 */ 
class Novedades extends BaseNovedades
{
    public function getCodigoPorModulo()
    {
        $codigo_interno = "Sin Codigo";
        if($this->getCodigoPrincipal()){
            switch($this->getModuloId())
            {
                case 1:
                    $com_interna = ComInternaPeer::retrieveByPK($this->getCodigoPrincipal());
                    $codigo_interno = $com_interna->getRadicado();
                break;
                case 2:
                    $com_enviada = ComEnviadaPeer::retrieveByPK($this->getCodigoPrincipal());
                    $codigo_interno = $com_enviada->getRadicado();
                break;
                case 3:
                    $com_recibida = ComRecibidaPeer::retrieveByPK($this->getCodigoPrincipal());
                    $codigo_interno = $com_recibida->getRadicado();
                break;
                default:
                    $codigo_interno = "Sin Codigo";
                break;
            }            
        }
        return $codigo_interno;
    }
}
