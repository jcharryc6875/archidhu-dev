<?php

/**
 * Subclass for performing query and update operations on the 'WF_PERMISO_TRANSICION_ACTIVIDAD' table.
 *
 * 
 *
 * @package lib.model
 */ 
class WfPermisoTransicionActividadPeer extends BaseWfPermisoTransicionActividadPeer
{
	/**
     * Executes the SQL query get data permisos de actividades por transicion.
     * 
     * @param id de la transición.     
     * @return array resultados
     * @throws SQLException If there is an error executing the specified query.
     */
    public static function getAllActividadesByTransicion($wftransicion_id)
    {
        $conexion = Propel::getConnection();
        $listdata = array();
        $query = "";
        if(is_numeric($wftransicion_id)){
            $query .= sprintf("SELECT DISTINCT %s.*,",WfActividadPeer::TABLE_NAME);
            $query .= sprintf("(SELECT %s FROM %s WHERE %s = $wftransicion_id AND %s = %s) AS SELECTED",WfPermisoTransicionActividadPeer::WF_PERMISO_TRANSICION_ACTIVIDAD_ID,WfPermisoTransicionActividadPeer::TABLE_NAME,WfPermisoTransicionActividadPeer::WF_TRANSICION_ID,WfPermisoTransicionActividadPeer::WF_ACTIVIDAD_ID,WfActividadPeer::WF_ACTIVIDAD_ID);
            $query .= sprintf(" FROM %s ORDER BY %s ASC",WfActividadPeer::TABLE_NAME,WfActividadPeer::DESCRIPCION);
            //*****************************************************************************
            //echo $query;exit;
            $sentencia = $conexion->prepare($query);
            $sentencia->execute();
            //$resultset = $sentencia->fetch(PDO::FETCH_BOTH);    
            //*****************************************************************************
            while($row = $sentencia->fetch(PDO::FETCH_BOTH)){
                $listdata[] = array($row['WF_ACTIVIDAD_ID'],$row['DESCRIPCION'],$row['SELECTED']);
            }
        }
        return $listdata;        
    }
	
    public static function getWfActividadesPorAccion($wf_transicion_id){
		$c = new Criteria();
        $c->add(WfPermisoTransicionActividadPeer::WF_TRANSICION_ID,$wf_transicion_id);
		$c->addAscendingOrderByColumn(WfActividadPeer::DESCRIPCION);
		$rs = WfPermisoTransicionActividadPeer::doSelectJoinWfActividad($c);
		return $rs; 	
	}
}
