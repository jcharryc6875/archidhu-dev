<?php

/**
 * Subclass for representing a row from the 'FACTURA' table.
 *
 * 
 *
 * @package lib.model
 */ 
class Factura extends BaseFactura
{
	function __toString(){		
		return sprintf("%s / %s",$this->getRadicado(),$this->getAsunto());
	}
	
    public function getRadicadoCustom($numero_radicado=1)
    {
        $radicado_custom = "";
        $codigo_ftipo = trim($this->getFacturaTipo()->getCodigo())  != null ? trim($this->getFacturaTipo()->getCodigo()) . "-" : "";
        $codigo_regional = trim($this->getRegional()->getCodigo()) != null ? trim($this->getRegional()->getCodigo()) . "-" : "";
        $codigo_entidad = trim($this->getRegional()->getEntidad()->getCodigo()) != null ? trim($this->getRegional()->getEntidad()->getCodigo()) . "-" : "";
        $periodo = $this->getPeriodoId();
        //$dependencia_cod = trim($this->getDependencia()->getCodigo());
		$dependencia_cod = "";
        $dependencia_cod = $dependencia_cod != null ? $dependencia_cod . "-" : "";
        //****************************************************************************************                
        $radicado_custom = sprintf("%s%s%s%s%05d-%s",$codigo_entidad,$codigo_ftipo,$codigo_regional,$dependencia_cod,$numero_radicado,$periodo);
        return $radicado_custom;
    }
    
    public function getEstadoFactByGen()
    {
        $estado_factura = 2;
        switch($this->getFacturaestadoId())
        {
            case 9;//Causado por Reclasificar
                $estado_factura = 2;//Causado
            break;
            case 16;//Porgramado Pago Reclasificar
                $estado_factura = 13;//Porgramado Pago
            break;
            case 17;//Factura Intervenida Reclasificar
                $estado_factura = 14;//Factura Intervenida
            break;
            case 18;//Pagada Reclasificar
                $estado_factura = 12;//Pagada
            break;
            default:
               $estado_factura = 2;
            break; 
        }
        return $estado_factura;
    }
    
    public function getStructHtmlUserByProcess($proceso_id,$itemid,$textitem="Enviar a")
    {
        $users_list = UsuarioPeer::getAllUserActiveFacturas($proceso_id);
        $item_html = "";
        if(count($users_list) != 1){
            $item_html .= '<div class="form-group">';                
            $item_html .= '<label for="'.md5($proceso_id.$itemid.$textitem).'" class="col-sm-2 control-label">'.$textitem.':</label>';
            $item_html .= '<div class="col-sm-6">';                                            
            $item_html .= '<select name="'.$itemid.'" id="'.$itemid.'" class="form-control input-sm required">';
            $item_html .= '<option value="">Seleccione...</option>';
            foreach($users_list as $usuario):
                $item_html .= '<option value="'.$usuario->getPrimaryKey().'">';
                $item_html .= $usuario->getNombre()." ".$usuario->getApellido();
                $item_html .= '</option>';
            endforeach;
            $item_html .= '</select>';
            $item_html .= '</div>';
            $item_html .= '</div>';
        }else{
            $item_html .= '<input type="hidden" name="'.$itemid.'" id="'.$itemid.'" value="'.$users_list[0]->getPrimaryKey().'"/>';
        }
        //**********************************************************************
        return $item_html;
    }
}
