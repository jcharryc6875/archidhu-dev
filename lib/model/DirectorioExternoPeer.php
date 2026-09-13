<?php

/**
 * Subclass for performing query and update operations on the 'DIRECTORIO_EXTERNO' table.
 *
 * 
 *
 * @package lib.model
 */ 
class DirectorioExternoPeer extends BaseDirectorioExternoPeer
{
    public static function addNewDirectorioExterno($fields,$isExists = false){
        $directorioexterno_id = null;        
        //************************************************************************************
        if($isExists){
            $directorioexterno_id = DirectorioExternoPeer::existsDirectorioExterno($fields);
            if(!is_null($directorioexterno_id)) { return $directorioexterno_id; }
        }
        //************************************************************************************
        try{
            $cnombre = isset($fields['NOMBRE']) ? trim($fields['NOMBRE']) : null;
            $tipo_identificacion = isset($fields['TIPO_IDENTIFICACION']) ? trim($fields['TIPO_IDENTIFICACION']) : null;        
            //************************************************************************************
            if(!trim($cnombre)){ return null; }
            //************************************************************************************
            $tipoidentificacion_id = TipoIdentificacionPeer::getTipoIdentificacionPkBySigla($tipo_identificacion);
            if(!trim($tipoidentificacion_id)){ return null; }
            //************************************************************************************
            $isInfoValid = DirectorioExternoPeer::validateInfoRemitente($fields);
            //if($isInfoValid['IsValid'] == false){ return $isInfoValid['MsgError']; }
            if($isInfoValid['IsValid'] == false){ return null; }
            //************************************************************************************
            $ciudad_codigo = isset($fields['CIUDAD_CODIGO']) ? trim($fields['CIUDAD_CODIGO']) : null;
            $ciudad_nombre = isset($fields['CIUDAD']) ? trim($fields['CIUDAD']) : null;
            $ciudad = CiudadPeer::getCiudadByNombAndCod($ciudad_nombre,$ciudad_codigo);
            //************************************************************************************
            if(is_null($ciudad)){ return null; }
            //************************************************************************************
            $directorio_externo = new DirectorioExterno();
            $directorio_externo->setUsuarioId($fields['USUARIO_RADICADOR']);//fata validar usuario
            $directorio_externo->setCiudadId($ciudad->getPrimaryKey());
            $directorio_externo->setTipopeticionarioId(isset($fields['TIPOPETICIONARIO_ID']) ? $fields['TIPOPETICIONARIO_ID'] : null);
            $directorio_externo->setTipoidentificacionId($tipoidentificacion_id);
            $directorio_externo->setNombre($cnombre);
            $directorio_externo->setNit(isset($fields['NUMERO_IDENTIFICACION']) ? $fields['NUMERO_IDENTIFICACION'] : null);
            $directorio_externo->setDireccion(isset($fields['DIRECCION']) ? $fields['DIRECCION'] : null);
            $directorio_externo->setFuncionario(isset($fields['FUNCIONARIO']) ? $fields['FUNCIONARIO'] : null);
            $directorio_externo->setCargo(isset($fields['CARGO']) ? $fields['CARGO'] : null);
            $directorio_externo->setTelefono(isset($fields['TELEFONO']) ? $fields['TELEFONO'] : null);
            $directorio_externo->setFax(isset($fields['FAX']) ? $fields['FAX'] : null);
            $directorio_externo->setEmail(isset($fields['EMAIL']) ? $fields['EMAIL'] : null);
            $directorio_externo->setPrefijo(isset($fields['PREFIJO']) ? $fields['PREFIJO'] : null);
            $directorio_externo->setEsPublico(1);
            $directorio_externo->setContEdicion(0);
            $directorio_externo->save();
            //************************************************************************************
            if($directorio_externo->getPrimaryKey()){
                $directorioexterno_id = $directorio_externo->getPrimaryKey();
            }
        } catch (PropelException $ex) {
            $msg_ex = $ex->getMessage();
            return null;
        } catch (\Exception $ex) {
            $msg_ex = $ex->getMessage();
            return null;
        } catch (\Throwable $ex) {
            $msg_ex = $ex->getMessage();
            return null;
        }
        //************************************************************************************
        return $directorioexterno_id;
    }
    
    public static function getDirectorioExternoByEmail($email) 
    {  
        try{
            if(!empty($email)){
                $c = new Criteria();
                $c->add(DirectorioExternoPeer::EMAIL,$email);
                $object  = DirectorioExternoPeer::doSelectOne($c);
            }else{
                $object = null;
            }
            //*******************************************************************************
            return $object;
        } catch (PropelException $x) {
            return null;
        } catch (\Exception $x) {
            return null;
        }
    }
    
    public static function existsDirectorioExterno($fields){
        $conexion = Propel::getConnection();
        $pkid = null;
        //************************************************************************************
        $cnombre = trim($fields['NOMBRE']) ? trim($fields['NOMBRE']) : null;
        //$cnombre = trim($fields['primer_nombre']) ? trim($fields['primer_nombre']) : "";
        //$cnombre .= trim($fields['segundo_nombre']) ? $cnombre.' '.trim($fields['primer_nombre']) : $cnombre;
        //$cnombre .= trim($fields['primer_apellido']) ? $cnombre.' '.trim($fields['primer_apellido']) : $cnombre;
        //$cnombre .= trim($fields['segundo_apellido']) ? $cnombre.' '.trim($fields['segundo_apellido']) : $cnombre;
        //************************************************************************************
        $query = "SELECT %s AS pkid FROM %s WHERE  %s = '" . $cnombre . "';";
        $query = sprintf($query, DirectorioExternoPeer::DIRECTORIOEXTERNO_ID, DirectorioExternoPeer::TABLE_NAME, DirectorioExternoPeer::NOMBRE);
    	//************************************************************************************
        $sentencia = $conexion->prepare($query);
        $sentencia->execute();
        $resultset = $sentencia->fetch(PDO::FETCH_OBJ);
        if($resultset->pkid){
            $pkid = $resultset->pkid;
        }
        //************************************************************************************
        $query = "SELECT %s AS pkid FROM %s WHERE  %s = '" . $fields['NUMERO_IDENTIFICACION'] . "';";
        $query = sprintf($query, DirectorioExternoPeer::DIRECTORIOEXTERNO_ID, DirectorioExternoPeer::TABLE_NAME, DirectorioExternoPeer::NIT);
    	//************************************************************************************
        $sentencia = $conexion->prepare($query);
        $sentencia->execute();
        $resultset = $sentencia->fetch(PDO::FETCH_OBJ);
        if($resultset->pkid){
            $pkid = $resultset->pkid;
        }
        //************************************************************************************
        return $pkid;
        //************************************************************************************
        $query = "SELECT %s AS pkid FROM %s WHERE  %s = '" . $fields['EMAIL'] . "';";
        $query = sprintf($query, DirectorioExternoPeer::DIRECTORIOEXTERNO_ID, DirectorioExternoPeer::TABLE_NAME, DirectorioExternoPeer::EMAIL);
    	//************************************************************************************
        $sentencia = $conexion->prepare($query);
        $sentencia->execute();
        $resultset = $sentencia->fetch(PDO::FETCH_OBJ);
        if($resultset->pkid){
            return $resultset->pkid;
        }
    }

    public static function validateInfoRemitente($fields){
        $msgerror = array();
        //************************************************************************************
        if(!isset($fields['NOMBRE'])){ $msgerror[] = "El parametro nombre es un campo obligatorio"; }
        if(!isset($fields['NUMERO_IDENTIFICACION'])){ $msgerror[] =  "El parametro numero de identifiación del remitente es un campo obligatorio"; }
        if(!isset($fields['CIUDAD_CODIGO'])){ $msgerror[] =  "El parametro código de la ciudad del remitente es un campo obligatorio"; }
        if(!isset($fields['CIUDAD'])){ $msgerror[] =  "El parametro nombre de la ciudad del remitente es un campo obligatorio"; }
        if(!isset($fields['TIPO_IDENTIFICACION'])){ $msgerror[] =  "El parametro tipo de identifiación del remitente es un campo obligatorio"; }
        //************************************************************************************
        if(!trim($fields['NOMBRE'])){ $msgerror[] =  "El nombre es un campo obligatorio"; }
        if(!trim($fields['NUMERO_IDENTIFICACION'])){ $msgerror[] =  "El numero de identifiación del remitente es un campo obligatorio"; }
        if(!trim($fields['CIUDAD_CODIGO'])){ $msgerror[] =  "El código de la ciudad del remitente es un campo obligatorio"; }
        if(!trim($fields['CIUDAD'])){ $msgerror[] =  "El nombre de la ciudad del remitente es un campo obligatorio"; }
        if(!trim($fields['TIPO_IDENTIFICACION'])){ $msgerror[] =  "El tipo de identifiación del remitente es un campo obligatorio"; }
        //************************************************************************************
        return array('IsValid' => (!count($msgerror) ? true : false), 'MsgError' => implode(";", $msgerror));
        //return array('IsValid' => (!count($msgerror) ? true : false), 'MsgError' => 'Error en info del remitente');
    }
}
