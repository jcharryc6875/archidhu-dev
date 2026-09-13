<?php

/**
 * Subclass for performing query and update operations on the 'ENVIADA_DIRECTORIO' table.
 *
 * 
 *
 * @package lib.model
 */ 
class EnviadaDirectorioPeer extends BaseEnviadaDirectorioPeer
{
    public static function borrarEnviadaDirectorios($com_enviadaId )
    { 
        $conexion = Propel::getConnection();
        $consulta = " delete FROM %s where %s =".$com_enviadaId;
        $sql      = sprintf($consulta,EnviadaDirectorioPeer::TABLE_NAME,EnviadaDirectorioPeer::COMENVIADA_ID);
        $sentencia = $conexion->prepare($sql);
        $sentencia->execute();
    }
    
    public static function insertaEnviadaDirectorios($directorios,$com_enviadaId,$rol)
    {
        $isValid = false;
        try{
            if($rol==1){
                $dir = $directorios;
                if(is_array($directorios)){
                    foreach ($directorios as $dircom) {
                        $objEnviadaDirectorio=new EnviadaDirectorio();
                        $objEnviadaDirectorio->setDirectorioexternoId($dircom);
                        $objEnviadaDirectorio->setComenviadaId($com_enviadaId);
                        $objEnviadaDirectorio->setRoldirenviadaId($rol);
                        $objEnviadaDirectorio->save();
                    }
                }else{
                    $objEnviadaDirectorio=new EnviadaDirectorio();
                    $objEnviadaDirectorio->setDirectorioexternoId($dir);
                    $objEnviadaDirectorio->setComenviadaId($com_enviadaId);
                    $objEnviadaDirectorio->setRoldirenviadaId($rol);
                    $objEnviadaDirectorio->save();
                }                
            }else{
                $arrDirectorios = is_array($directorios) ? $directorios : explode(',',$directorios);		
                foreach($arrDirectorios as $dir){			
                    if($dir!=0){
                        $objEnviadaUsuario=new EnviadaDirectorio();
                        $objEnviadaUsuario->setDirectorioexternoId($dir);
                        $objEnviadaUsuario->setComenviadaId($com_enviadaId);
                        $objEnviadaUsuario->setRoldirenviadaId($rol);
                        $objEnviadaUsuario->save();					
                    }
                }     			
            }
            //************************************************************************************
            $isValid = true;
        }catch (Exception $e){
            //echo 'Excepci�n capturada: ',  $e->getMessage(), "\n";
            $isValid = false;
        }
        //****************************************************************************************
        return $isValid;
    }

    public static function getCopiaExterna($com_enviadaId)
    {
        $c=new Criteria();
        $c->add(EnviadaDirectorioPeer::COMENVIADA_ID, $com_enviadaId);
        $c->add(EnviadaDirectorioPeer::ROLDIRENVIADA_ID, 2);
        //$c->addAscendingOrderByColumn(CominternaUsuarioPeer::COMINTERNAUSUARIO_ID);
        $result = EnviadaDirectorioPeer::doSelect($c);    
        $dirCopia=array();
        foreach($result as $res){				
            $dirCopia[] = $res->getDirectorioExterno(); 					
        }
        return $dirCopia;	
    }

    public static function getListEnviadaDirByRol($comenviada_id, $rol_id = 1)
    {
        $c=new Criteria();
        $c->add(EnviadaDirectorioPeer::COMENVIADA_ID, $comenviada_id);
        $c->add(EnviadaDirectorioPeer::ROLDIRENVIADA_ID, $rol_id);
        return EnviadaDirectorioPeer::doSelect($c);
    }
	
	public static function getEnviadaDirByRolObject($comenviada_id, $rol_id = 1)
    {
        $c=new Criteria();
        $c->add(EnviadaDirectorioPeer::COMENVIADA_ID, $comenviada_id);
        $c->add(EnviadaDirectorioPeer::ROLDIRENVIADA_ID, $rol_id);
        return EnviadaDirectorioPeer::doSelectOne($c);
    }
}
