<?php

/**
 * Subclass for performing query and update operations on the 'FACTURA' table.
 *
 * 
 *
 * @package lib.model
 */ 
class FacturaPeer extends BaseFacturaPeer
{
    static public function executeCustomQueryData($query)
    {
        $conexion = Propel::getConnection();
		$sentencia = $conexion->prepare($query);
        return $sentencia->execute();
    }
    
    static public function getFirstUserByProcessId($factura_id=0,$process_id=0, $ejecutada=true, $roluser_id=2)
    {
        $c = new Criteria();
        $c->addJoin(FactUsuarioDestinoPeer::FACTURAVITACORA_ID,FacturaVitacoraPeer::FACTURAVITACORA_ID);
        $c->add(FactUsuarioDestinoPeer::FACTURAVITACORAROLUSUARIO_ID,$roluser_id);
        $c->add(FacturaVitacoraPeer::FACTURA_ID,$factura_id);
        $c->add(FacturaVitacoraPeer::FACTURAPROCESO_ID,$process_id);
        $c->add(FacturaVitacoraPeer::EJECUTADA,$ejecutada);
        $c->addAscendingOrderByColumn(FacturaVitacoraPeer::FACTURAVITACORA_ID);
        $list = FactUsuarioDestinoPeer::doSelect($c);
        //*********************************************************************
        $usuario_select = 0;
        foreach($list as $item)
        {
            if($item->getUsuarioId())
            {
                $usuario_select = $item->getUsuarioId();
            }
        }
        return $usuario_select;
    }
    
    public static function getComObjectByRadicado($radicado,$periodo_id)
    {
    	$c = new Criteria();
        $c->add(FacturaPeer::RADICADO,$radicado);
        $c->add(FacturaPeer::PERIODO_ID,$periodo_id);
        $com_object = FacturaPeer::doSelectOne($c);
    	//************************************************************************************
        if($com_object != null){
            return $com_object;
        }else{
            return null;
        }
    }
    
    static public function getTextFirstUserByProcessId($factura_id=0,$process_id=0, $ejecutada=true, $roluser_id=2)
    {
        $c = new Criteria();
        $c->addJoin(FactUsuarioDestinoPeer::FACTURAVITACORA_ID,FacturaVitacoraPeer::FACTURAVITACORA_ID);
        $c->add(FactUsuarioDestinoPeer::FACTURAVITACORAROLUSUARIO_ID,$roluser_id);
        $c->add(FacturaVitacoraPeer::FACTURA_ID,$factura_id);
        $c->add(FacturaVitacoraPeer::FACTURAPROCESO_ID,$process_id);
        $c->add(FacturaVitacoraPeer::EJECUTADA,$ejecutada);
        $c->addAscendingOrderByColumn(FacturaVitacoraPeer::FACTURAVITACORA_ID);
        $list = FactUsuarioDestinoPeer::doSelect($c);
        //*********************************************************************
        $usuario_select = "";
        foreach($list as $item)
        {
            if($item->getUsuarioId())
            {
                $usuario_select = $item->getUsuario();
            }
        }
        return $usuario_select;
    }
    
    static public function getTextUserExecuteActByProcessId($factura_id=0,$process_id=0, $ejecutada=true, $roluser_id=1)
    {
        $c = new Criteria();
        $c->addJoin(FacturaVitacoraUsuarioPeer::FACTURAVITACORA_ID,FacturaVitacoraPeer::FACTURAVITACORA_ID);
        $c->add(FacturaVitacoraUsuarioPeer::FACTURAVITACORAROLUSUARIO_ID,$roluser_id);
        $c->add(FacturaVitacoraPeer::FACTURA_ID,$factura_id);
        $c->add(FacturaVitacoraPeer::FACTURAPROCESO_ID,$process_id);
        $c->add(FacturaVitacoraPeer::EJECUTADA,$ejecutada);
        $c->addAscendingOrderByColumn(FacturaVitacoraPeer::FACTURAVITACORA_ID);
        $list = FacturaVitacoraUsuarioPeer::doSelect($c);
        //*********************************************************************
        $usuario_select = "";
        foreach($list as $item)
        {
            if($item->getUsuarioId())
            {
                $usuario_select = $item->getUsuario();
            }
        }
        return $usuario_select;
    }
    
    static public function getObsUserRespByProcessId($factura_id=0,$process_id=0, $ejecutada=true, $factestado_id=-1)
    {
        $c = new Criteria();        
        $c->add(FacturaVitacoraPeer::FACTURA_ID,$factura_id);
        $c->add(FacturaVitacoraPeer::FACTURAPROCESO_ID,$process_id);
        $c->add(FacturaVitacoraPeer::FACTURAESTADO_ID,$factestado_id);
        $c->add(FacturaVitacoraPeer::EJECUTADA,$ejecutada);
        $c->addDescendingOrderByColumn(FacturaVitacoraPeer::FACTURAVITACORA_ID);
        $list = FacturaVitacoraPeer::doSelect($c);
        //*********************************************************************
        $data_text = "";
        foreach($list as $item)
        {
            $data_text = $item->getObservaciones();
        }
        return $data_text;
    }
    
    static public function getFechaEjecUserRespByProcessId($factura_id=0,$process_id=0, $ejecutada=true, $factestado_id=-1)
    {
        $c = new Criteria();        
        $c->add(FacturaVitacoraPeer::FACTURA_ID,$factura_id);
        $c->add(FacturaVitacoraPeer::FACTURAPROCESO_ID,$process_id);
        $c->add(FacturaVitacoraPeer::FACTURAESTADO_ID,$factestado_id);
        $c->add(FacturaVitacoraPeer::EJECUTADA,$ejecutada);
        $c->addDescendingOrderByColumn(FacturaVitacoraPeer::FACTURAVITACORA_ID);
        $list = FacturaVitacoraPeer::doSelect($c);
        //*********************************************************************
        $data_text = "";
        foreach($list as $item)
        {
            $data_text = $item->getFechaF();
        }
        return $data_text;
    }
    
    static public function getAccessByUserOrigen($factura_id=0)
    {
        $usuariologuiado = sfContext::getInstance()->getUser()->getAttribute('usuario_id', '', 'subscriber');
        $entidad_conectado = sfContext::getInstance()->getUser()->getAttribute('entidad_id', '', 'subscriber');
        $regional_conectado = sfContext::getInstance()->getUser()->getAttribute('regional_id', '', 'subscriber');
        //**********************************************************************************************
        $c = new Criteria();
        $c->addJoin(FacturaVitacoraUsuarioPeer::FACTURAVITACORA_ID,FacturaVitacoraPeer::FACTURAVITACORA_ID);
        $c->add(FacturaVitacoraPeer::FACTURA_ID,$factura_id);
        $c->add(FacturaVitacoraUsuarioPeer::USUARIO_ID,$usuariologuiado);        
        return FacturaVitacoraPeer::doCount($c);
    }
    
    static public function getUserIdByFirtsRadicador($factura_id=0)
    {
        $usuariologuiado = sfContext::getInstance()->getUser()->getAttribute('usuario_id', '', 'subscriber');
        $entidad_conectado = sfContext::getInstance()->getUser()->getAttribute('entidad_id', '', 'subscriber');
        $regional_conectado = sfContext::getInstance()->getUser()->getAttribute('regional_id', '', 'subscriber');
        //**********************************************************************************************
        $c = new Criteria();
        $c->addJoin(FacturaVitacoraUsuarioPeer::FACTURAVITACORA_ID,FacturaVitacoraPeer::FACTURAVITACORA_ID);
        $c->add(FacturaVitacoraPeer::FACTURA_ID,$factura_id);
        $c->addAscendingOrderByColumn(FacturaVitacoraUsuarioPeer::FACTURAVITACORA_ID);
        $fvitacora_user =FacturaVitacoraUsuarioPeer::doSelectOne($c);
        if($fvitacora_user != null)
            return $fvitacora_user->getUsuarioId();
        else
            return $usuariologuiado;
    }

    static public function getAccessByUserDestino($factura_id=0)
    {
        $usuariologuiado = sfContext::getInstance()->getUser()->getAttribute('usuario_id', '', 'subscriber');
        $entidad_conectado = sfContext::getInstance()->getUser()->getAttribute('entidad_id', '', 'subscriber');
        $regional_conectado = sfContext::getInstance()->getUser()->getAttribute('regional_id', '', 'subscriber');
        //**********************************************************************************************
        $c = new Criteria();
        $c->addJoin(FactUsuarioDestinoPeer::FACTURAVITACORA_ID,FacturaVitacoraPeer::FACTURAVITACORA_ID);
        $c->add(FacturaVitacoraPeer::FACTURA_ID,$factura_id);
        $c->add(FactUsuarioDestinoPeer::USUARIO_ID,$usuariologuiado);        
        return FacturaVitacoraPeer::doCount($c);
    }
    
    static public function addAuditActionImage($factura_id,$filename,$typeAction="delete")
    {
        $usuariologuiado = sfContext::getInstance()->getUser()->getAttribute('usuario_id', '', 'subscriber');
        $entidad_conectado = sfContext::getInstance()->getUser()->getAttribute('entidad_id', '', 'subscriber');
        $regional_conectado = sfContext::getInstance()->getUser()->getAttribute('regional_id', '', 'subscriber');
        //**********************************************************************************************
        $fact_audit = new FacturaAuditImage();
        $fact_audit->setUsuarioId($usuariologuiado);
        $fact_audit->setFacturaId($factura_id);
        $fact_audit->setFechaCreacion(date("Y-m-d G:i:s"));
        $fact_audit->setFilename($filename);
        $fact_audit->setTypeAction($typeAction);
        $fact_audit->save();
    }
    
    public static function getBasicUrlAttachFacturas($usuario_id,$periodo = null,$useTmp = false)
    {
    	$array_url = array();
        $usuario = UsuarioPeer::retrieveByPK($usuario_id);
    	//*****************************************************************************************
    	$usuariologuiado = $usuario->getUserName();
    	$entidad_conectado = $usuario->getRegional()->getEntidadId();
    	$regional_conectado = $usuario->getRegionalId();
    	$periodo = trim($periodo) ? trim($periodo) : date("Y");
    	//*****************************CREANDO ESTRUCTURA DE DIRECTORIOS***************************
    	$entidad = EntidadPeer::retrieveByPk($entidad_conectado);
    	$regional = RegionalPeer::retrieveByPk($regional_conectado);
    	$dirRaiz = ParametroPeer::retrieveByPk(9)->getValortexto();
        $alias_str = ParametroPeer::retrieveByPk(12)->getValortexto();
        //*****************************************************************************************
    	$dir_tmp  = $useTmp ? ParametroPeer::retrieveByPk(65)->getValortexto() : "";
        $dir_facturas = 'Facturas';
    	//*****************************************************************************************
        $entidad_folder = trim($regional->getEntidad()->getDirectorioName());
        $regional_folder = trim($regional->getDirectorioName());
	    $entidad_text = $entidad_folder ? $entidad_folder : "";
        $entidad_text = $entidad_text ? ($regional_folder ? $entidad_folder.DIRECTORY_SEPARATOR.$regional_folder : $entidad_folder) : "";        
        $directorio_attach = simad_util::NormalizePath($dirRaiz.$entidad_text.DIRECTORY_SEPARATOR.$dir_facturas.DIRECTORY_SEPARATOR.$periodo);
        $directorio_tmp = $useTmp ? simad_util::NormalizePath($dirRaiz.$entidad_folder.DIRECTORY_SEPARATOR.$dir_tmp) : "";
        $basic_path = $entidad_text .DIRECTORY_SEPARATOR. $dir_facturas .DIRECTORY_SEPARATOR. $periodo;
        $alias_web = str_replace("\\","/",$basic_path);
    	//*****************************************************************************************
    	$array_url['basic_path'] = $basic_path;
    	$array_url['full_path']  = $directorio_attach;
    	$array_url['temp_path']  = $directorio_tmp;
        $array_url['alias_web']  = $alias_str.$alias_web;
    	//*****************************************************************************************
    	return $array_url;
    }
    
    static public function getUrlBase() 
    {
		$http = 'http';
		if(!empty($_SERVER["HTTPS"])){
			$http .= "s";
		}		
		$url = $http . "://".$_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"];
		$filename = explode("/", $url);		
		$base = "";
		for( $i = 0; $i < (count($filename) - 1); ++$i ) {
			$base .= $filename[$i].'/';
		}
		return $base;
	}
}
