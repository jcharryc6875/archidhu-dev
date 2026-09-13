<?php

/**
 * Subclass for performing query and update operations on the 'ESTADISTICA' table.
 *
 * 
 *
 * @package lib.model
 */ 

//spout
use Box\Spout\Writer\Common\Creator\WriterEntityFactory;
use Box\Spout\Common\Entity\Row;
use Box\Spout\Writer\Common\Creator\Style\StyleBuilder;
use Box\Spout\Common\Entity\Style\Color;

class EstadisticaPeer extends BaseEstadisticaPeer
{
    public static function getSpecialResultadoFinalComRecibidasConRespuesta($resultado_conresp, $resultado_sinresp, $resultado_total) 
    {
        try 
        {
            $mas_alto = max(array(count($resultado_sinresp), count($resultado_conresp), count($resultado_total)));
            //*************************************************************************************
            $array_labels = array();
            $array_totales = array();
            //*************************************************************************************
            $array_conresp = array();
            $array_sinresp = array();
            $array_total_rad = array();
            //*************************************************************************************
            $idx = 0;
            $idx2 = 0;
            for($i=0; $i < $mas_alto; $i++)
            {
                $anio_mes = $resultado_total[$i]['ANIO_MES'];

                if(in_array($anio_mes, $resultado_sinresp[$idx]))
                {
                    $array_sinresp[] = (int)$resultado_sinresp[$idx]['TOTAL'];
                    $idx++;
                } 
                else
                {
                    $array_sinresp[] = 0;
                }

                if(in_array($anio_mes, $resultado_conresp[$idx2]))
                {
                    $array_conresp[] = (int)$resultado_conresp[$idx2]['TOTAL'];
                    $idx2++;
                } 
                else
                {
                    $array_conresp[] = 0; 
                }

                if(in_array($anio_mes, $resultado_total[$i]))
                {
                    $array_total_rad[] = (int)$resultado_total[$i]['TOTAL'];
                } 
                else
                {
                    $array_total_rad[] = 0; 
                }

                $array_labels[] = $resultado_total[$i]['MES_TEXT'] . ' ' .$resultado_total[$i]['VIGENCIA'];
            }
            //*************************************************************************************
            $ilist_data4 = array();
            $ilist_data4['totales'] = array( 'total_radicados' => $array_total_rad, 'total_sin_resp' => $array_sinresp, 'total_con_resp' => $array_conresp );
            //*************************************************************************************
            $data_views = array();
            $data_views[] = array('resultado' => $resultado_total, 'dataviews' => $ilist_data4);
            //*************************************************************************************
            $array_final = array();
            for ($i = 0; $i <= count($data_views[0]['resultado']); $i++)
            {
                $array_final[$i]['VIGENCIA'] = $data_views[0]['resultado'][$i]["VIGENCIA"];
                $array_final[$i]['MES'] = $data_views[0]['resultado'][$i]["MES_TEXT"];
                $array_final[$i]['CON_RESPUESTA'] = $data_views[0]['dataviews']['totales']['total_con_resp'][$i];
                $array_final[$i]['SIN_RESPUESTA'] = $data_views[0]['dataviews']['totales']['total_sin_resp'][$i];
                $array_final[$i]['TOTAL_RADICADOS'] = $data_views[0]['dataviews']['totales']['total_radicados'][$i];
            }
            //*************************************************************************************
            return $array_final;
        } 
        catch (PropelException $th) 
        {
            return array();
        } 
        catch (\Exception $th) 
        {
            return array();
        } 
        catch (\Throwable $th) 
        {
            return array();
        }
    }

    public static function getSpecialResultadoFinalComEnviadasGestionadas($resultado5, $resultado_total) 
    {
        try 
        {
            $mas_alto = max(array(count($resultado5), count($resultado_total)));
            //*************************************************************************************
            $data_views5 = array();
            $array_labels5 = array();
            //*************************************************************************************
            $array_totales_gestionadas = array();
            $array_total_radicadas = array();
            //*************************************************************************************
            $idx = 0;
            for($i=0; $i < $mas_alto; $i++)
            {
                $anio_mes = $resultado_total[$i]['ANIO_MES'];
                $array_labels5[] = sprintf("%s %s",ucwords($resultado_total[$i]['MES_TEXT']),$resultado_total[$i]['VIGENCIA']);

                if(in_array($anio_mes, $resultado5[$idx]))
                {
                    $array_totales_gestionadas[] = (int)$resultado5[$idx]['TOTAL'];
                    $idx++;
                } 
                else
                {
                    $array_totales_gestionadas[] = 0;
                }

                if(in_array($anio_mes, $resultado_total[$i]))
                {
                    $array_total_radicadas[] = (int)$resultado_total[$i]['TOTAL'];
                } 
                else
                {
                    $array_total_radicadas[] = 0;
                }
            }
            //*************************************************************************************
            //$ilist_data5['labels'] = $array_labels5;
            $ilist_data5 = array();
            $ilist_data5['total_gestionados'] = $array_totales_gestionadas;
            $ilist_data5['total_radicadas'] = $array_total_radicadas;

            $data_views = array();
            $data_views[] = array('resultado' => $resultado5, 'dataviews' => $ilist_data5);
            //*************************************************************************************
            $array_final = array();

            for ($i = 0; $i <= count($data_views[0]['resultado']); $i++)
            {
                $array_final[$i]['VIGENCIA'] = $data_views[0]['resultado'][$i]["VIGENCIA"];
                $array_final[$i]['MES'] = $data_views[0]['resultado'][$i]["MES_TEXT"];
                $array_final[$i]['TOTAL'] = $data_views[0]['resultado'][$i]["TOTAL"];
            }
            //*************************************************************************************
            return $array_final;
        } 
        catch (PropelException $th) 
        {
            return array();
        } 
        catch (\Exception $th) 
        {
            return array();
        } 
        catch (\Throwable $th) 
        {
            return array();
        }
    }

    public static function getComRecibidaPorDependencia($params = array()) 
    {
        try 
        {
            if(!count($params) || !isset($params['fecha_inicial']) || !isset($params['fecha_final']))
            {
                return array();
            }
            else if(empty($params['fecha_inicial']) || empty($params['fecha_final']))
            {
                return array();
            }
            //****************************************************************************
            $c = new Criteria();
            $c->addJoin(ComRecibidaPeer::DEPENDENCIA_ID, DependenciaPeer::DEPENDENCIA_ID);
            $c->clearSelectColumns();
            $c->addAsColumn('TOTAL', 'COUNT(*)');
            $c->addSelectColumn(DependenciaPeer::NOMBRE);
            $c->add(ComRecibidaPeer::FECHA_CREACION, $params['fecha_inicial'], Criteria::GREATER_THAN);  
            $c->addAnd(ComRecibidaPeer::FECHA_CREACION, $params['fecha_final'], Criteria::LESS_THAN); 
            $c->addGroupByColumn(DependenciaPeer::NOMBRE);
            //****************************************************************************
            return ComRecibidaPeer::doSelectStmt($c)->fetchAll(PDO::FETCH_ASSOC);
        } 
        catch (PropelException $th) 
        {
            return array();
        } 
        catch (\Exception $th) 
        {
            return array();
        } 
        catch (\Throwable $th) 
        {
            return array();
        }
    }

    public static function getComRecibidaPorTramites($params = array()) 
    {
        try 
        {
            if(!count($params) || !isset($params['fecha_inicial']) || !isset($params['fecha_final']))
            {
                return array();
            }
            else if(empty($params['fecha_inicial']) || empty($params['fecha_final']))
            {
                return array();
            }
            //****************************************************************************
            $c = new Criteria();
            //****************************************************************************
            $c->addJoin(ComRecibidaPeer::COMRECIBIDA_ID, ComrecibidaUsuarioPeer::COMRECIBIDA_ID);
            $c->addJoin(ComRecibidaPeer::TIPOCOMRECIBIDA_ID, TipoComRecibidaPeer::TIPOCOMRECIBIDA_ID);
            $c->add(ComrecibidaUsuarioPeer::ROLUSUARIORECIBIDAID, array(2),Criteria::IN);
            $c->add(ComrecibidaUsuarioPeer::ESTADOCOMRECIBIDA_ID, array(11,14,12),Criteria::NOT_IN);
            //****************************************************************************
            $c->clearSelectColumns();
            //****************************************************************************
            $c->addAsColumn("TOTAL","COUNT(DISTINCT ".ComrecibidaUsuarioPeer::COMRECIBIDA_ID.")");
            $c->addSelectColumn(ComRecibidaPeer::TIPOCOMRECIBIDA_ID);
            $c->addSelectColumn(TipoComRecibidaPeer::DESCRIPCION);
            //****************************************************************************
            $c->add(ComRecibidaPeer::FECHA_CREACION,$params['fecha_inicial'].' 00:00:00',Criteria::GREATER_EQUAL);
            $c->addAnd(ComRecibidaPeer::FECHA_CREACION,$params['fecha_final'].' 23:59:59',Criteria::LESS_EQUAL);
            if(isset($params['periodo_id'])){ $c->add(ComRecibidaPeer::PERIODO_ID,$params['periodo_id']); }
            //****************************************************************************
            if(isset($params['apply_user_filter'])){
                if($params['apply_user_filter'] == true && $params['current_user'] != null){
                    if($params['current_user']->getReceptorDep()){
                        $c->add(ComRecibidaPeer::DEPENDENCIA_ID,$params['current_user']->getDependenciaId());
                        $c->addOr(ComrecibidaUsuarioPeer::USUARIO_ID,$params['current_user']->getPrimaryKey());
                    }else{
                        $c->add(ComrecibidaUsuarioPeer::USUARIO_ID,$params['current_user']->getPrimaryKey());
                    }
                }
            }
            //****************************************************************************
            $c->addGroupByColumn(ComRecibidaPeer::TIPOCOMRECIBIDA_ID);
            $c->addGroupByColumn(TipoComRecibidaPeer::DESCRIPCION); 
            //****************************************************************************
            return ComRecibidaPeer::doSelectStmt($c)->fetchAll(PDO::FETCH_ASSOC);
        } 
        catch (PropelException $th) 
        {
            return array();
        } 
        catch (\Exception $th) 
        {
            return array();
        } 
        catch (\Throwable $th) 
        {
            return array();
        }
    }

    public static function getComRecibidaPorMedioRecepcion($params = array()) 
    {
        try {
            if(!count($params) || !isset($params['fecha_inicial']) || !isset($params['fecha_final']))
            {
                return array();
            }
            else if(empty($params['fecha_inicial']) || empty($params['fecha_final']))
            {
                return array();
            }
            //****************************************************************************
            //TERCERA CONSULTA
            $c = new Criteria();
            $c->addJoin(ComRecibidaPeer::FORMARECEPCION_ID, FormaRecepcionPeer::FORMARECEPCION_ID);
            $c->clearSelectColumns();
            //****************************************************************************
            $c->addAsColumn("TOTAL","COUNT(DISTINCT ".ComRecibidaPeer::COMRECIBIDA_ID.")");
            $c->addSelectColumn(ComRecibidaPeer::FORMARECEPCION_ID);
            $c->addSelectColumn(FormaRecepcionPeer::DESCRIPCION);
            $c->add(ComRecibidaPeer::FECHA_CREACION, $params['fecha_inicial'], Criteria::GREATER_THAN);  
            $c->addAnd(ComRecibidaPeer::FECHA_CREACION, $params['fecha_final'], Criteria::LESS_THAN); 
            $c->addGroupByColumn(ComRecibidaPeer::FORMARECEPCION_ID);
            $c->addGroupByColumn(FormaRecepcionPeer::DESCRIPCION);
            //****************************************************************************
            return ComRecibidaPeer::doSelectStmt($c)->fetchAll(PDO::FETCH_ASSOC);
        } 
        catch (PropelException $th) 
        {
            return array();
        } 
        catch (\Exception $th) 
        {
            return array();
        } 
        catch (\Throwable $th) 
        {
            return array();
        }
    }

    public static function getCantidad($sql,$fechaInicial,$fechaFinal) {
        try {
            if(trim($sql) != ""){
                $periodoActual=date("Y");
                $conexion = Propel::getConnection();
                $sql = sprintf($sql,"'".$fechaInicial." 00:00:00'","'".$fechaFinal." 23:59:59'");
                $data = array();
                //*************************************************************************
                $sentencia = $conexion->prepare($sql);
                $sentencia->execute();
                $list_rs = $sentencia->fetchAll();
    
                foreach ($list_rs as $item) {
                    $data[] = array('titulo' => $item[0], 'total' => $item[1]);
                }
                return $data;
            }else{
                return array();
            }
        } catch (PropelException $th) {
            return array();
        } catch (Exception $th) {
            return array();
        }
    }

    public static function getComRecibidaConRespuesta($params = array()) 
    {
        try 
        {
            if(!count($params) || !isset($params['fecha_inicial']) || !isset($params['fecha_final']))
            {
                return array();
            }
            else if(empty($params['fecha_inicial']) || empty($params['fecha_final']))
            {
                return array();
            }
            //****************************************************************************
            $c = new Criteria();
            //****************************************************************************
            $c->addJoin(ComRecibidaPeer::COMRECIBIDA_ID, ComrecibidaUsuarioPeer::COMRECIBIDA_ID);
            $c->add(ComrecibidaUsuarioPeer::ROLUSUARIORECIBIDAID, array(2),Criteria::IN);
            $c->add(ComrecibidaUsuarioPeer::ESTADOCOMRECIBIDA_ID, array(11,14,12),Criteria::NOT_IN);
            //****************************************************************************
            $c->addJoin(ComRecibidaPeer::COMENVIADA_ID, ComEnviadaPeer::COMENVIADA_ID);
            $c->add(ComRecibidaPeer::FECHA_CREACION,$params['fecha_inicial'].' 00:00:00',Criteria::GREATER_EQUAL);
            $c->addAnd(ComRecibidaPeer::FECHA_CREACION,$params['fecha_final'].' 23:59:59',Criteria::LESS_EQUAL);
            if(isset($params['periodo_id'])){ $c->add(ComRecibidaPeer::PERIODO_ID,$params['periodo_id']); }
            //****************************************************************************
            if(isset($params['apply_user_filter'])){
                if($params['apply_user_filter'] == true && $params['current_user'] != null){
                    if($params['current_user']->getReceptorDep()){
                        $c->add(ComRecibidaPeer::DEPENDENCIA_ID,$params['current_user']->getDependenciaId());
                        $c->addOr(ComrecibidaUsuarioPeer::USUARIO_ID,$params['current_user']->getPrimaryKey());
                    }else{
                        $c->add(ComrecibidaUsuarioPeer::USUARIO_ID,$params['current_user']->getPrimaryKey());
                    }
                }
            }
            //****************************************************************************
            $c->addGroupByColumn("MONTH(".ComRecibidaPeer::FECHA_CREACION.")");
            $c->addGroupByColumn("YEAR(".ComRecibidaPeer::FECHA_CREACION.")");
            $c->addAscendingOrderByColumn("YEAR(".ComRecibidaPeer::FECHA_CREACION.")");
            $c->addAscendingOrderByColumn("MONTH(".ComRecibidaPeer::FECHA_CREACION.")");
            //****************************************************************************
            $c->clearSelectColumns();
            //****************************************************************************
            $c->addAsColumn("TOTAL","COUNT(DISTINCT ".ComrecibidaUsuarioPeer::COMRECIBIDA_ID.")");
            $c->addAsColumn("MES_NUMBER","MONTH(".ComRecibidaPeer::FECHA_CREACION.")");
            $c->addAsColumn("VIGENCIA","YEAR(".ComRecibidaPeer::FECHA_CREACION.")");
            $c->addAsColumn("MES_TEXT","FORMAT(DATEFROMPARTS(1900,MONTH(".ComRecibidaPeer::FECHA_CREACION."), 1), 'MMMM', 'es-ES')");
            $c->addAsColumn("ANIO_MES","CONCAT(YEAR(".ComRecibidaPeer::FECHA_CREACION."), '_', FORMAT(DATEFROMPARTS(1900,MONTH(".ComRecibidaPeer::FECHA_CREACION."), 1), 'MMMM', 'es-ES'))");
            //****************************************************************************
            return ComRecibidaPeer::doSelectStmt($c)->fetchAll(PDO::FETCH_ASSOC);
        } 
        catch (PropelException $th) 
        {
            return array();
        } 
        catch (\Exception $th) 
        {
            return array();
        } 
        catch (\Throwable $th) 
        {
            return array();
        }
    }

    public static function getComRecibidaSinRespuesta($params = array()) 
    {
        try 
        {
            if(!count($params) || !isset($params['fecha_inicial']) || !isset($params['fecha_final']))
            {
                return array();
            }
            else if(empty($params['fecha_inicial']) || empty($params['fecha_final']))
            {
                return array(); 
            }
            //****************************************************************************
            $d = new Criteria();
            //****************************************************************************
            $d->addJoin(ComRecibidaPeer::COMRECIBIDA_ID, ComrecibidaUsuarioPeer::COMRECIBIDA_ID);
            $d->add(ComrecibidaUsuarioPeer::ROLUSUARIORECIBIDAID, array(2),Criteria::IN);
            $d->add(ComrecibidaUsuarioPeer::ESTADOCOMRECIBIDA_ID, array(11,14,12),Criteria::NOT_IN);
            //****************************************************************************
            $d->add(ComRecibidaPeer::COMENVIADA_ID,null,Criteria::ISNULL);
            $d->addOr(ComRecibidaPeer::COMENVIADA_ID,'');
            $d->add(ComRecibidaPeer::FECHA_CREACION,$params['fecha_inicial'].' 00:00:00',Criteria::GREATER_EQUAL);
            $d->addAnd(ComRecibidaPeer::FECHA_CREACION,$params['fecha_final'].' 23:59:59',Criteria::LESS_EQUAL);
            if(isset($params['periodo_id'])){ $d->add(ComRecibidaPeer::PERIODO_ID,$params['periodo_id']); }
            //****************************************************************************
            if(isset($params['apply_user_filter'])){
                if($params['apply_user_filter'] == true && $params['current_user'] != null){
                    if($params['current_user']->getReceptorDep()){
                        $d->add(ComRecibidaPeer::DEPENDENCIA_ID,$params['current_user']->getDependenciaId());
                        $d->addOr(ComrecibidaUsuarioPeer::USUARIO_ID,$params['current_user']->getPrimaryKey());
                    }else{
                        $d->add(ComrecibidaUsuarioPeer::USUARIO_ID,$params['current_user']->getPrimaryKey());
                    }
                }
            }
            //****************************************************************************
            $d->addGroupByColumn("MONTH(".ComRecibidaPeer::FECHA_CREACION.")");
            $d->addGroupByColumn("YEAR(".ComRecibidaPeer::FECHA_CREACION.")");
            $d->addAscendingOrderByColumn("YEAR(".ComRecibidaPeer::FECHA_CREACION.")");
            $d->addAscendingOrderByColumn("MONTH(".ComRecibidaPeer::FECHA_CREACION.")");
            //****************************************************************************
            $d->clearSelectColumns();
            /****************************************************************************/
            $d->addAsColumn("TOTAL","COUNT(DISTINCT ".ComrecibidaUsuarioPeer::COMRECIBIDA_ID.")");
            $d->addAsColumn("MES_NUMBER","MONTH(".ComRecibidaPeer::FECHA_CREACION.")");
            $d->addAsColumn("VIGENCIA","YEAR(".ComRecibidaPeer::FECHA_CREACION.")");
            $d->addAsColumn("MES_TEXT","FORMAT(DATEFROMPARTS(1900,MONTH(".ComRecibidaPeer::FECHA_CREACION."), 1), 'MMMM', 'es-ES')");
            $d->addAsColumn("ANIO_MES","CONCAT(YEAR(".ComRecibidaPeer::FECHA_CREACION."), '_', FORMAT(DATEFROMPARTS(1900,MONTH(".ComRecibidaPeer::FECHA_CREACION."), 1), 'MMMM', 'es-ES'))");
            //****************************************************************************
            return ComRecibidaPeer::doSelectStmt($d)->fetchAll(PDO::FETCH_ASSOC);
        } 
        catch (PropelException $th) 
        {
            return array();
        } 
        catch (\Exception $th) 
        {
            return array();
        } 
        catch (\Throwable $th) 
        {
            return array();
        }
    }

    public static function getComRecibidaSinRespuestaAllUsers($params = array()) 
    {
        try 
        {
            if(!count($params) || !isset($params['fecha_inicial']) || !isset($params['fecha_final']))
            {
                return array();
            }
            else if(empty($params['fecha_inicial']) || empty($params['fecha_final']))
            {
                return array(); 
            }
            //****************************************************************************
            $d = new Criteria();
            $d->add(ComRecibidaPeer::COMENVIADA_ID,null,Criteria::ISNULL);
            $d->addOr(ComRecibidaPeer::COMENVIADA_ID,'');
            $d->add(ComRecibidaPeer::FECHA_CREACION,$params['fecha_inicial'].' 00:00:00',Criteria::GREATER_EQUAL);
            $d->addAnd(ComRecibidaPeer::FECHA_CREACION,$params['fecha_final'].' 23:59:59',Criteria::LESS_EQUAL);
            if(isset($params['periodo_id'])){ $d->add(ComRecibidaPeer::PERIODO_ID,$params['periodo_id']); }
            $d->add(ComRecibidaUsuarioPeer::ROLUSUARIORECIBIDAID, 2);
            //****************************************************************************
            $d->addJoin(ComRecibidaPeer::COMRECIBIDA_ID, ComrecibidaUsuarioPeer::COMRECIBIDA_ID);
            $d->addJoin(ComrecibidaUsuarioPeer::USUARIO_ID, UsuarioPeer::USUARIO_ID); //NUEVA
            //****************************************************************************
            $d->addGroupByColumn("MONTH(".ComRecibidaPeer::FECHA_CREACION.")");
            $d->addGroupByColumn("YEAR(".ComRecibidaPeer::FECHA_CREACION.")");

            $d->addAscendingOrderByColumn("YEAR(".ComRecibidaPeer::FECHA_CREACION.")");
            $d->addAscendingOrderByColumn("MONTH(".ComRecibidaPeer::FECHA_CREACION.")");
            //****************************************************************************
            $d->clearSelectColumns();
            /****************************************************************************/
            $d->addAsColumn("TOTAL","COUNT(DISTINCT ".ComrecibidaUsuarioPeer::COMRECIBIDA_ID.")");
            $d->addAsColumn("MES_NUMBER","MONTH(".ComRecibidaPeer::FECHA_CREACION.")");
            $d->addAsColumn("VIGENCIA","YEAR(".ComRecibidaPeer::FECHA_CREACION.")");
            $d->addAsColumn("MES_TEXT","FORMAT(DATEFROMPARTS(1900,MONTH(".ComRecibidaPeer::FECHA_CREACION."), 1), 'MMMM', 'es-ES')");
            $d->addAsColumn("ANIO_MES","CONCAT(YEAR(".ComRecibidaPeer::FECHA_CREACION."), '_', FORMAT(DATEFROMPARTS(1900,MONTH(".ComRecibidaPeer::FECHA_CREACION."), 1), 'MMMM', 'es-ES'))");
            //****************************************************************************
            return ComRecibidaPeer::doSelectStmt($d)->fetchAll(PDO::FETCH_ASSOC);
        } 
        catch (PropelException $th) 
        {
            return array();
        } 
        catch (\Exception $th) 
        {
            return array();
        } 
        catch (\Throwable $th) 
        {
            return array();
        }
    }

    public static function getComRecibidaSinRespuestaByUser($params = array()) 
    {
        try 
        {
            if(!count($params) || !isset($params['fecha_inicial']) || !isset($params['fecha_final']))
            {
                return array();
            }
            else if(empty($params['fecha_inicial']) || empty($params['fecha_final']))
            {
                return array(); 
            }
            //****************************************************************************
            $d = new Criteria();
            $d->add(ComRecibidaPeer::COMENVIADA_ID,null,Criteria::ISNULL);
            $d->addOr(ComRecibidaPeer::COMENVIADA_ID,'');
            $d->add(ComRecibidaPeer::FECHA_CREACION,$params['fecha_inicial'].' 00:00:00',Criteria::GREATER_EQUAL);
            $d->addAnd(ComRecibidaPeer::FECHA_CREACION,$params['fecha_final'].' 23:59:59',Criteria::LESS_EQUAL);
            if(isset($params['periodo_id'])){ $d->add(ComRecibidaPeer::PERIODO_ID,$params['periodo_id']); }
            $d->add(ComRecibidaUsuarioPeer::ROLUSUARIORECIBIDAID, 2);
            //****************************************************************************
            $d->addJoin(ComRecibidaPeer::COMRECIBIDA_ID, ComrecibidaUsuarioPeer::COMRECIBIDA_ID);
            $d->addJoin(ComrecibidaUsuarioPeer::USUARIO_ID, UsuarioPeer::USUARIO_ID); //NUEVA
            //****************************************************************************
            $d->addGroupByColumn(UsuarioPeer::USUARIO_ID);  // NUEVA
            $d->addGroupByColumn("CONCAT(" . UsuarioPeer::NOMBRE . ", ' ', " . UsuarioPeer::APELLIDO . ")");  // NUEVA
            $d->addGroupByColumn("MONTH(".ComRecibidaPeer::FECHA_CREACION.")");
            $d->addGroupByColumn("YEAR(".ComRecibidaPeer::FECHA_CREACION.")");
            //****************************************************************************
            $d->addAscendingOrderByColumn("YEAR(".ComRecibidaPeer::FECHA_CREACION.")");
            $d->addAscendingOrderByColumn("MONTH(".ComRecibidaPeer::FECHA_CREACION.")");
            //****************************************************************************
            $d->clearSelectColumns();
            //****************************************************************************
            $d->addAsColumn("TOTAL","COUNT(DISTINCT ".ComrecibidaUsuarioPeer::COMRECIBIDA_ID.")");
            $d->addAsColumn("USUARIO_ASIGNADO", "CONCAT(" . UsuarioPeer::NOMBRE . ", ' ', " . UsuarioPeer::APELLIDO . ")");  // NUEVA
            $d->addAsColumn("USUARIO_ID", UsuarioPeer::USUARIO_ID);  // NUEVA
            $d->addAsColumn("MES_NUMBER","MONTH(".ComRecibidaPeer::FECHA_CREACION.")");
            $d->addAsColumn("VIGENCIA","YEAR(".ComRecibidaPeer::FECHA_CREACION.")");
            $d->addAsColumn("MES_TEXT","FORMAT(DATEFROMPARTS(1900,MONTH(".ComRecibidaPeer::FECHA_CREACION."), 1), 'MMMM', 'es-ES')");
            $d->addAsColumn("ANIO_MES","CONCAT(YEAR(".ComRecibidaPeer::FECHA_CREACION."), '_', FORMAT(DATEFROMPARTS(1900,MONTH(".ComRecibidaPeer::FECHA_CREACION."), 1), 'MMMM', 'es-ES'))");
            //****************************************************************************
            return ComRecibidaPeer::doSelectStmt($d)->fetchAll(PDO::FETCH_ASSOC);
        } 
        catch (PropelException $th) 
        {
            return array();
        } 
        catch (\Exception $th) 
        {
            return array();
        } 
        catch (\Throwable $th) 
        {
            return array();
        }
    }

    public static function getComRecibidaTotales($params = array())  
    {
        try {
            if(!count($params) || !isset($params['fecha_inicial']) || !isset($params['fecha_final']))
            {
                return array();
            }
            else if(empty($params['fecha_inicial']) || empty($params['fecha_final']))
            {
                return array();
            }
            //****************************************************************************
            $e = new Criteria();
            $e->addJoin(ComRecibidaPeer::COMRECIBIDA_ID, ComrecibidaUsuarioPeer::COMRECIBIDA_ID, Criteria::INNER_JOIN);
            //****************************************************************************
            $e->add(ComRecibidaPeer::FECHA_CREACION,$params['fecha_inicial'].' 00:00:00',Criteria::GREATER_EQUAL);
            $e->addAnd(ComRecibidaPeer::FECHA_CREACION,$params['fecha_final'].' 23:59:59',Criteria::LESS_EQUAL);
            if(isset($params['periodo_id'])){ $e->add(ComRecibidaPeer::PERIODO_ID,$params['periodo_id']); }
            //****************************************************************************
            if(isset($params['apply_user_filter'])){
                if($params['apply_user_filter'] == true && $params['current_user'] != null){
                    if($params['current_user']->getReceptorDep()){
                        $e->add(ComRecibidaPeer::DEPENDENCIA_ID,$params['current_user']->getDependenciaId());
                        $e->addOr(ComrecibidaUsuarioPeer::USUARIO_ID,$params['current_user']->getPrimaryKey());
                    }else{
                        $e->add(ComrecibidaUsuarioPeer::USUARIO_ID,$params['current_user']->getPrimaryKey());
                    }
                }
            }
            //****************************************************************************
            $e->add(ComrecibidaUsuarioPeer::ROLUSUARIORECIBIDAID, array(2),Criteria::IN);
            $e->add(ComrecibidaUsuarioPeer::ESTADOCOMRECIBIDA_ID, array(11,12,14),Criteria::NOT_IN);
            //****************************************************************************
            $e->addGroupByColumn("MONTH(".ComRecibidaPeer::FECHA_CREACION.")");
            $e->addGroupByColumn("YEAR(".ComRecibidaPeer::FECHA_CREACION.")");
            $e->addAscendingOrderByColumn("YEAR(".ComRecibidaPeer::FECHA_CREACION.")");
            $e->addAscendingOrderByColumn("MONTH(".ComRecibidaPeer::FECHA_CREACION.")");
            //****************************************************************************
            $e->clearSelectColumns();
            //****************************************************************************
            $e->addAsColumn("TOTAL","COUNT(DISTINCT ".ComrecibidaUsuarioPeer::COMRECIBIDA_ID.")");
            $e->addAsColumn("MES_NUMBER","MONTH(".ComRecibidaPeer::FECHA_CREACION.")");
            $e->addAsColumn("VIGENCIA","YEAR(".ComRecibidaPeer::FECHA_CREACION.")");
            $e->addAsColumn("MES_TEXT","FORMAT(DATEFROMPARTS(1900,MONTH(".ComRecibidaPeer::FECHA_CREACION."), 1), 'MMMM', 'es-ES')");
            $e->addAsColumn("ANIO_MES","CONCAT(YEAR(".ComRecibidaPeer::FECHA_CREACION."), '_', FORMAT(DATEFROMPARTS(1900,MONTH(".ComRecibidaPeer::FECHA_CREACION."), 1), 'MMMM', 'es-ES'))");
            //****************************************************************************
            return ComRecibidaPeer::doSelectStmt($e)->fetchAll(PDO::FETCH_ASSOC);
        } 
        catch (PropelException $th) 
        {
            return array();
        } 
        catch (\Exception $th) 
        {
            return array();
        } 
        catch (\Throwable $th) 
        {
            return array();
        }
    }

    public static function getComEnviadaGestionadas($params = array())  
    {
        try {
            if(!count($params) || !isset($params['fecha_inicial']) || !isset($params['fecha_final']))
            {
                return array();
            }
            else if(empty($params['fecha_inicial']) || empty($params['fecha_final']))
            {
                return array();
            }
            //****************************************************************************
            $c = new Criteria();
            $c->addJoin(ComEnviadaPeer::COMENVIADA_ID,EnviadaUsuarioPeer::COMENVIADA_ID);
            $c->addJoin(ComEnviadaPeer::COMENVIADA_ID,ServicioPeer::CONSECUTIVOCOM_ID);
            //******************************************************************************
            $c->add(ServicioPeer::MODULO_ID,ModulesEnable::ComEnviada);
            $c->add(EnviadaUsuarioPeer::ROLUSCOMENVIADA_ID,1);
            $c->add(EnviadaUsuarioPeer::ESTADOCOMENVIADA_ID,array(1,4),Criteria::NOT_IN);

            $c->add(ComEnviadaPeer::FECHA_CREACION,$params['fecha_inicial'].' 00:00:00',Criteria::GREATER_EQUAL);
            $c->addAnd(ComEnviadaPeer::FECHA_CREACION,$params['fecha_final'].' 23:59:59',Criteria::LESS_EQUAL);
            //****************************************************************************
            $c->addGroupByColumn("MONTH(".ComEnviadaPeer::FECHA_CREACION.")");
            $c->addGroupByColumn("YEAR(".ComEnviadaPeer::FECHA_CREACION.")");
            $c->addAscendingOrderByColumn("YEAR(".ComEnviadaPeer::FECHA_CREACION.")");
            $c->addAscendingOrderByColumn("MONTH(".ComEnviadaPeer::FECHA_CREACION.")");
            //****************************************************************************
            $c->clearSelectColumns();
            //****************************************************************************
            $c->addAsColumn("TOTAL","COUNT(*)");
            $c->addAsColumn("MES_NUMBER","MONTH(".ComEnviadaPeer::FECHA_CREACION.")");
            $c->addAsColumn("VIGENCIA","YEAR(".ComEnviadaPeer::FECHA_CREACION.")");
            $c->addAsColumn("MES_TEXT","FORMAT(DATEFROMPARTS(1900,MONTH(".ComEnviadaPeer::FECHA_CREACION."), 1), 'MMMM', 'es-ES')");
            $c->addAsColumn("ANIO_MES","CONCAT(YEAR(".ComEnviadaPeer::FECHA_CREACION."), '_', FORMAT(DATEFROMPARTS(1900,MONTH(".ComEnviadaPeer::FECHA_CREACION."), 1), 'MMMM', 'es-ES'))");
            //****************************************************************************
            return ComEnviadaPeer::doSelectStmt($c)->fetchAll();
        } 
        catch (PropelException $th) 
        {
            return array();
        } 
        catch (\Exception $th) 
        {
            return array();
        } 
        catch (\Throwable $th) 
        {
            return array();
        }
    }

    public static function getAllComEnviadasMes($params = array())  
    {
        try {
            if(!count($params) || !isset($params['fecha_inicial']) || !isset($params['fecha_final']))
            {
                return array();
            }
            else if(empty($params['fecha_inicial']) || empty($params['fecha_final']))
            {
                return array();
            }
            //****************************************************************************
            $c = new Criteria();
            $c->addJoin(ComEnviadaPeer::COMENVIADA_ID,EnviadaUsuarioPeer::COMENVIADA_ID);
            //******************************************************************************
            $c->add(EnviadaUsuarioPeer::ROLUSCOMENVIADA_ID,1);
            $c->add(EnviadaUsuarioPeer::ESTADOCOMENVIADA_ID,array(1,4),Criteria::NOT_IN);

            $c->add(ComEnviadaPeer::FECHA_CREACION,$params['fecha_inicial'].' 00:00:00',Criteria::GREATER_EQUAL);
            $c->addAnd(ComEnviadaPeer::FECHA_CREACION,$params['fecha_final'].' 23:59:59',Criteria::LESS_EQUAL);
            //****************************************************************************
            $c->addGroupByColumn("MONTH(".ComEnviadaPeer::FECHA_CREACION.")");
            $c->addGroupByColumn("YEAR(".ComEnviadaPeer::FECHA_CREACION.")");
            $c->addAscendingOrderByColumn("YEAR(".ComEnviadaPeer::FECHA_CREACION.")");
            $c->addAscendingOrderByColumn("MONTH(".ComEnviadaPeer::FECHA_CREACION.")");
            //****************************************************************************
            $c->clearSelectColumns();
            //****************************************************************************
            $c->addAsColumn("TOTAL","COUNT(*)");
            $c->addAsColumn("MES_NUMBER","MONTH(".ComEnviadaPeer::FECHA_CREACION.")");
            $c->addAsColumn("VIGENCIA","YEAR(".ComEnviadaPeer::FECHA_CREACION.")");
            $c->addAsColumn("MES_TEXT","FORMAT(DATEFROMPARTS(1900,MONTH(".ComEnviadaPeer::FECHA_CREACION."), 1), 'MMMM', 'es-ES')");
            $c->addAsColumn("ANIO_MES","CONCAT(YEAR(".ComEnviadaPeer::FECHA_CREACION."), '_', FORMAT(DATEFROMPARTS(1900,MONTH(".ComEnviadaPeer::FECHA_CREACION."), 1), 'MMMM', 'es-ES'))");
            //****************************************************************************
            return ComEnviadaPeer::doSelectStmt($c)->fetchAll();
        } 
        catch (PropelException $th) 
        {
            return array();
        } 
        catch (\Exception $th) 
        {
            return array();
        } 
        catch (\Throwable $th) 
        {
            return array();
        }
    }

    public static function getServicioByTipo($params = array())  
    {
        try 
        {
            if(!count($params) || !isset($params['fecha_inicial']) || !isset($params['fecha_final']))
            {
                return array();
            }
            else if(empty($params['fecha_inicial']) || empty($params['fecha_final']))
            {
                return array();
            }
            //****************************************************************************
            $c = new Criteria();
            $c->addJoin(ServicioPeer::TIPOSERVICIO_ID, TipoServicioPeer::TIPOSERVICIO_ID);
            $c->add(ServicioPeer::FECHA_CREACION,$params['fecha_inicial'].' 00:00:00',Criteria::GREATER_EQUAL);
            $c->addAnd(ServicioPeer::FECHA_CREACION,$params['fecha_final'].' 23:59:59',Criteria::LESS_EQUAL);
            //****************************************************************************
            $c->addGroupByColumn("MONTH(".ServicioPeer::FECHA_CREACION.")");
            $c->addGroupByColumn("YEAR(".ServicioPeer::FECHA_CREACION.")");
            $c->addGroupByColumn(ServicioPeer::TIPOSERVICIO_ID);
            $c->addGroupByColumn(TipoServicioPeer::DESCRIPCION);
            $c->addAscendingOrderByColumn("YEAR(".ServicioPeer::FECHA_CREACION.")");
            $c->addAscendingOrderByColumn("MONTH(".ServicioPeer::FECHA_CREACION.")");
            //****************************************************************************
            $c->clearSelectColumns();  
            //****************************************************************************
            $c->addAsColumn("TOTAL","COUNT(*)");
            $c->addAsColumn("NOMBRE_SERVICIO", TipoServicioPeer::DESCRIPCION);
            $c->addAsColumn("MES_NUMBER","MONTH(".ServicioPeer::FECHA_CREACION.")");
            $c->addAsColumn("VIGENCIA","YEAR(".ServicioPeer::FECHA_CREACION.")");
            $c->addAsColumn("MES_TEXT","FORMAT(DATEFROMPARTS(1900,MONTH(".ServicioPeer::FECHA_CREACION."), 1), 'MMMM', 'es-ES')");
            $c->addAsColumn("ANIO_MES","CONCAT(YEAR(".ServicioPeer::FECHA_CREACION."), '_', FORMAT(DATEFROMPARTS(1900,MONTH(".ServicioPeer::FECHA_CREACION."), 1), 'MMMM', 'es-ES'))");
            //****************************************************************************
            return ServicioPeer::doSelectStmt($c)->fetchAll();
        } 
        catch (PropelException $th) 
        {
            return array();
        } 
        catch (\Exception $th) 
        {
            return array();
        } 
        catch (\Throwable $th) 
        {
            return array();
        }
    }

    public static function getServicioByEstado($params = array())  
    {
        try 
        {
            if(!count($params) || !isset($params['fecha_inicial']) || !isset($params['fecha_final']))
            {
                return array();
            }
            else if(empty($params['fecha_inicial']) || empty($params['fecha_final']))
            {
                return array();
            }
            //****************************************************************************
            $c = new Criteria();
            $c->addJoin(ServicioPeer::SERVICIOESTADO_ID, ServicioEstadoPeer::SERVICIOESTADO_ID);
            $c->add(ServicioPeer::FECHA_CREACION,$params['fecha_inicial'].' 00:00:00',Criteria::GREATER_EQUAL);
            $c->addAnd(ServicioPeer::FECHA_CREACION,$params['fecha_final'].' 23:59:59',Criteria::LESS_EQUAL);
            //****************************************************************************
            $c->addGroupByColumn("MONTH(".ServicioPeer::FECHA_CREACION.")");
            $c->addGroupByColumn("YEAR(".ServicioPeer::FECHA_CREACION.")");
            $c->addGroupByColumn(ServicioPeer::SERVICIOESTADO_ID);
            $c->addGroupByColumn(ServicioEstadoPeer::DESCRIPCION);
            $c->addAscendingOrderByColumn("YEAR(".ServicioPeer::FECHA_CREACION.")");
            $c->addAscendingOrderByColumn("MONTH(".ServicioPeer::FECHA_CREACION.")");
            //****************************************************************************
            $c->clearSelectColumns();
            //****************************************************************************
            $c->addAsColumn("TOTAL","COUNT(*)");
            $c->addAsColumn("ESTADO_SERVICIO", ServicioEstadoPeer::DESCRIPCION);
            $c->addAsColumn("MES_NUMBER","MONTH(".ServicioPeer::FECHA_CREACION.")");
            $c->addAsColumn("VIGENCIA","YEAR(".ServicioPeer::FECHA_CREACION.")");
            $c->addAsColumn("MES_TEXT","FORMAT(DATEFROMPARTS(1900,MONTH(".ServicioPeer::FECHA_CREACION."), 1), 'MMMM', 'es-ES')");
            $c->addAsColumn("ANIO_MES","CONCAT(YEAR(".ServicioPeer::FECHA_CREACION."), '_', FORMAT(DATEFROMPARTS(1900,MONTH(".ServicioPeer::FECHA_CREACION."), 1), 'MMMM', 'es-ES'))");
            //****************************************************************************
            return ServicioPeer::doSelectStmt($c)->fetchAll();
        } 
        catch (PropelException $th) 
        {
            return array();
        } 
        catch (\Exception $th) 
        {
            return array();
        } 
        catch (\Throwable $th) 
        {
            return array();
        }
    }

    public static function getDataLineChart2($params = array())
    {
        $ilist_data5 = array();
        try 
        {
            $allusers_array = EstadisticaPeer::getComRecibidaSinRespuestaAllUsers($params); 
            $byuser_array = EstadisticaPeer::getComRecibidaSinRespuestaByUser($params); 
            //***********************************************************************************************************
            $meses_array = [];
            foreach($allusers_array as $mes_array)
            {
                if(!in_array(trim($mes_array['MES_TEXT']), $meses_array))
                {
                    $meses_array[] = ucfirst(trim($mes_array['MES_TEXT']));
                }
            }
            //***********************************************************************************************************
            $mes_actual = (int)date('n');
            $meses = [1 => 'enero', 2 => 'febrero', 3 => 'marzo', 4 => 'abril', 5 => 'mayo', 6 => 'junio', 7 => 'julio', 8 => 'agosto', 9 => 'septiembre', 10 => 'octubre', 11 => 'noviembre', 12 => 'diciembre'];
            //***********************************************************************************************************
            $usuarios_unicos = [];
            foreach ($byuser_array as $record) 
            {
                $usuario_id = $record['USUARIO_ID'];
                $usuario_nombre = $record['USUARIO_ASIGNADO'];
                
                if (!isset($usuarios_unicos[$usuario_id])) 
                {
                    $usuarios_unicos[$usuario_id] = $usuario_nombre;
                }
            }
            //***********************************************************************************************************
            $labels_usuarios = array_values($usuarios_unicos);
            $records_por_usuario = [];
            //***********************************************************************************************************
            foreach ($usuarios_unicos as $usuario_id => $usuario_nombre) 
            {
                $records_por_usuario[$usuario_id] = ['USUARIO_ID' => $usuario_id, 'USUARIO_ASIGNADO' => $usuario_nombre, 'meses' => []];
                
                //*******************************************************************************************************
                for ($mes = 1; $mes <= $mes_actual; $mes++) 
                {
                    $records_por_usuario[$usuario_id]['meses'][$mes] = ['TOTAL' => '0', 'MES_NUMBER' => (string)$mes, 'VIGENCIA' => '2025', 'MES_TEXT' => $meses[$mes], 'ANIO_MES' => '2025_' . $meses[$mes]];
                }
            }
            //***********************************************************************************************************
            foreach ($byuser_array as $record) 
            {
                $usuario_id = $record['USUARIO_ID'];
                $mes_number = (int)$record['MES_NUMBER'];
                //*******************************************************************************************************
                $records_por_usuario[$usuario_id]['meses'][$mes_number] = ['TOTAL' => $record['TOTAL'], 'MES_NUMBER' => $record['MES_NUMBER'], 'VIGENCIA' => $record['VIGENCIA'], 'MES_TEXT' => $record['MES_TEXT'], 'ANIO_MES' => $record['ANIO_MES']];
            }
            //***********************************************************************************************************
            $totales_por_usuario = [];
            foreach ($records_por_usuario as $usuario_id => $usuario_data) 
            {
                $dataset['label'] = $usuario_data['USUARIO_ASIGNADO'];
                $dsvalores = array();
                foreach ($usuario_data['meses'] as $mes => $mes_data) 
                {
                    $dsvalores[] = (int)$mes_data['TOTAL'];
                }

                $dataset['data'] = $dsvalores;
                $dataset['fill'] = true;
                $totales_por_usuario[] = $dataset;

            }
            //***********************************************************************************************************
            $ilist_data5['labels'] = $meses_array;
            $ilist_data5['labels_usuarios'] = $labels_usuarios;
            $ilist_data5['totales'] = $totales_por_usuario;
            //***********************************************************************************************************
            return $ilist_data5;
        } catch (PropelException $th) {
            return null;
        } catch (\Exception $th) {
            return null;
        } catch (\Throwable $th) {
            return null;
        }
    }

    public static function getDataBarStacked($params = array())
    {
        $ilist_data4 = array();
        try {
            $resultado_sinresp = EstadisticaPeer::getComRecibidaSinRespuesta($params); 
            $resultado_total = EstadisticaPeer::getComRecibidaTotales($params); 
            $resultado_conresp = EstadisticaPeer::getComRecibidaConRespuesta($params);
            $mas_alto = max(array(count($resultado_sinresp), count($resultado_conresp), count($resultado_total)));
            //**************************************************************************************
            $array_labels = array();
            $array_totales = array();
            $array_conresp = array();
            $array_sinresp = array();
            $array_total_rad = array();
            //**************************************************************************************
            $idx = 0;
            $idx2 = 0;
            for($i=0; $i < $mas_alto; $i++)
            {
                $anio_mes = $resultado_total[$i]['ANIO_MES'];

                if(in_array($anio_mes, $resultado_sinresp[$idx]))
                {
                    $array_sinresp[] = (int)$resultado_sinresp[$idx]['TOTAL'];
                    $idx++;
                } 
                else
                {
                    $array_sinresp[] = 0;
                }

                if(in_array($anio_mes, $resultado_conresp[$idx2]))
                {
                    $array_conresp[] = (int)$resultado_conresp[$idx2]['TOTAL'];
                    $idx2++;
                } 
                else
                {
                    $array_conresp[] = 0; 
                }

                if(in_array($anio_mes, $resultado_total[$i]))
                {
                    $array_total_rad[] = (int)$resultado_total[$i]['TOTAL'];
                } 
                else
                {
                    $array_total_rad[] = 0; 
                }

                $array_labels[] = $resultado_total[$i]['MES_TEXT'] . ' ' .$resultado_total[$i]['VIGENCIA'];
            }
            //************************************************************************************
            $ilist_data4['labels'] = $array_labels;
            $ilist_data4['totales'] = array( 'total_radicados' => $array_total_rad, 'total_sin_resp' => $array_sinresp, 'total_con_resp' => $array_conresp );
            $ilist_data4['resultado_total'] = $resultado_total;
            //**************************************************************************************
            return $ilist_data4;
        } catch (PropelException $th) {
            return null;
        } catch (\Exception $th) {
            return null;
        } catch (\Throwable $th) {
            return null;
        }
    }
    
    public static function getDependenciasList($params = array())
    {
        $ilist_data4 = array();
        try {

            $c = new Criteria();

            // SELECT
            $c->addSelectColumn(ComRecibidaPeer::DEPENDENCIA_ID);
            $c->addAsColumn('TOTAL', 'COUNT(1)');
            // JOIN
            $c->addJoin(ComRecibidaPeer::COMRECIBIDA_ID, ComrecibidaUsuarioPeer::COMRECIBIDA_ID, Criteria::INNER_JOIN);
            // WHERE
            $c->add(ComrecibidaUsuarioPeer::ROLUSUARIORECIBIDAID, 2);
            $c->add(ComrecibidaUsuarioPeer::ESTA_ASIGNADA, true);
            $c->add(ComRecibidaPeer::MARCA_VINCULACION, false);
            $c->add(ComRecibidaPeer::COMENVIADA_ID, null,Criteria::ISNULL);

            $c->add(ComRecibidaPeer::FECHA_CREACION, $params['fecha_inicial'], Criteria::GREATER_EQUAL); //'2025-12-01 00:00:00' 
            $c->addAnd(ComRecibidaPeer::FECHA_CREACION, $params['fecha_final'], Criteria::LESS_EQUAL); //2025-12-22 23:59:59
            // GROUP BY
            $c->addGroupByColumn(ComRecibidaPeer::DEPENDENCIA_ID);
            // DISTINCT
            $c->setDistinct();

            // EJECUCIÓN
            $result = ComRecibidaPeer::doSelectStmt($c)->fetchAll(PDO::FETCH_ASSOC);
            return $result;
        } catch (PropelException $th) {
            return null;
        } catch (\Exception $th) {
            return null;
        } catch (\Throwable $th) {
            return null;
        }
    }

    public static function getUsersByDependenciaList($params = array(), $dependencia_id = null)
    {
        try {
            $c = new Criteria();

            // SELECT
            $c->addSelectColumn(ComRecibidaPeer::DEPENDENCIA_ID);
            // CONCATENACIÓN SQL SERVER
            $c->addAsColumn('USUARIO_ASIGNADO', "USUARIO.NOMBRE + ' ' + USUARIO.APELLIDO");
            $c->addAsColumn('TOTAL_ASIGNADAS', 'COUNT(1)');
            $c->addAsColumn('NOMBRE_DEPENDENCIA', DependenciaPeer::NOMBRE);
            // JOINS
            $c->addJoin(ComRecibidaPeer::COMRECIBIDA_ID, ComrecibidaUsuarioPeer::COMRECIBIDA_ID, Criteria::INNER_JOIN);
            $c->addJoin(ComrecibidaUsuarioPeer::USUARIO_ID, UsuarioPeer::USUARIO_ID, Criteria::INNER_JOIN);
            $c->addJoin(ComRecibidaPeer::DEPENDENCIA_ID, DependenciaPeer::DEPENDENCIA_ID, Criteria::INNER_JOIN);
            // WHERE
            $c->add(ComrecibidaUsuarioPeer::ROLUSUARIORECIBIDAID, 2);
            $c->add(ComRecibidaPeer::DEPENDENCIA_ID, $dependencia_id);
            $c->add(ComRecibidaPeer::FECHA_CREACION, $params['fecha_inicial'], Criteria::GREATER_EQUAL); //'2025-12-15 00:00:00'
            $c->addAnd(ComRecibidaPeer::FECHA_CREACION, $params['fecha_final'], Criteria::LESS_EQUAL); //2025-12-22 23:59:59

            // GROUP BY
            $c->addGroupByColumn(ComRecibidaPeer::DEPENDENCIA_ID);
            $c->addGroupByColumn(UsuarioPeer::NOMBRE);
            $c->addGroupByColumn(UsuarioPeer::APELLIDO);
            $c->addGroupByColumn(DependenciaPeer::NOMBRE);

            // DISTINCT
            $c->setDistinct();

            // EJECUCIÓN
            return ComRecibidaPeer::doSelectStmt($c)->fetchAll(PDO::FETCH_ASSOC);
        } catch (PropelException $th) {
            return null;
        } catch (\Exception $th) {
            return null;
        } catch (\Throwable $th) {
            return null;
        }
    }

    public static function getUsersAreaByListDetallado($params = array(), $dependencia_id = null)
    {
        try {

            $c = new Criteria();
            $c->setDistinct();

            // SELECT
            $c->addAsColumn('USUARIO_ASIGNADO', "CONCAT(".UsuarioPeer::NOMBRE.",' ',".UsuarioPeer::APELLIDO.")");
            $c->addAsColumn('DEPENDENCIA_NOMBRE', DependenciaPeer::NOMBRE);
            $c->addAsColumn('DEPENDENCIA_CODIGO', DependenciaPeer::CODIGO);
            $c->addAsColumn('COM_ASUNTO', ComRecibidaPeer::ASUNTO);
            $c->addAsColumn('COM_FECHA_RADICACION', ComRecibidaPeer::FECHA_CREACION);
            $c->addAsColumn('COM_RADICADO', ComRecibidaPeer::RADICADO);
            // JOINS
            $c->addJoin(ComRecibidaPeer::COMRECIBIDA_ID, ComrecibidaUsuarioPeer::COMRECIBIDA_ID, Criteria::INNER_JOIN);
            $c->addJoin(ComrecibidaUsuarioPeer::USUARIO_ID, UsuarioPeer::USUARIO_ID, Criteria::INNER_JOIN);
            $c->addJoin(ComRecibidaPeer::DEPENDENCIA_ID, DependenciaPeer::DEPENDENCIA_ID, Criteria::INNER_JOIN);
            // WHERE
            $c->add(ComrecibidaUsuarioPeer::ROLUSUARIORECIBIDAID, 2);
            $c->add(ComRecibidaPeer::DEPENDENCIA_ID, $dependencia_id);
            $c->add(ComRecibidaPeer::FECHA_CREACION, $params['fecha_inicial'], Criteria::GREATER_EQUAL);
            $c->addAnd(ComRecibidaPeer::FECHA_CREACION, $params['fecha_final'], Criteria::LESS_EQUAL);            

            // EJECUCIÓN
            return ComRecibidaPeer::doSelectStmt($c)->fetchAll(PDO::FETCH_ASSOC);
        } catch (PropelException $th) {
            return null;
        } catch (\Exception $th) {
            return null;
        } catch (\Throwable $th) {
            return null;
        }
    }

    public static function getDataDonutByElements($ilist_data = array())
    {
        $ilist_data4 = array();
        try {
            $nro_items_y_axis = count($ilist_data);
            $caracteres = 'abcdefghijklmnopqrstuvwyz';
            $array_labels = array();
            $array_totales = array();
            $array_ensamble = array('x' => 'Recibidas por tramite');
            $array_ykeys = array();
            //**************************************************************************************
            foreach ($ilist_data as $rows) {
                $foo = $caracteres[rand(0, strlen($caracteres) - 1)];
                $array_labels[] = $rows['DESCRIPCION'];
                $array_totales[] = $rows['TOTAL'];
                $array_ensamble[$foo] = $rows['TOTAL'];
                $array_ykeys[] = $foo; 
            }
            //**************************************************************************************
            $ilist_data4['labels'] = $array_labels;
            $ilist_data4['totales'] = $array_totales;
            $ilist_data4['ensamble'] = $array_ensamble;
            $ilist_data4['ykeys'] = $array_ykeys;
            //**************************************************************************************
            return $ilist_data4;
        } catch (PropelException $th) {
            return $ilist_data4;
        } catch (\Exception $th) {
            return $ilist_data4;
        } catch (\Throwable $th) {
            return $ilist_data4;
        }
    }


    /**
     * Metodo generatePendingReport(). 
     * Genera el reporte com_recibidas pendientes de responder por dependencia 
     * y lo envia al jefe de dicha dependencia
     * Los parametros de entrada son la fecha inicial y la fecha final del lapso de tiempo a reportar
     */
    public static function generatePendingReport($fecha_inicio = null, $fecha_fin = null)
    {
        $params = [];
        $params['fecha_inicial'] = $fecha_inicio ? $fecha_inicio . ' 00:00:00' : '2025-12-01 00:00:00';
        $params['fecha_final'] = $fecha_fin ? $fecha_fin . ' 23:59:59' : '2025-12-22 23:59:59';
        $response = array();
        //***********************************************************************************************************
        try {
            $lista_dependencias = EstadisticaPeer::getDependenciasList($params);
            //*******************************************************************************************************
            $lista_usuarios_consolidados = [];
            foreach ($lista_dependencias as $dependencia) {
                $lista_usuarios_consolidados[] = EstadisticaPeer::getUsersByDependenciaList($params, $dependencia['DEPENDENCIA_ID']);
            }
            //*******************************************************************************************************
            foreach ($lista_usuarios_consolidados as $grupo_dependencias) {
                $final_info_dependencia = [];
                foreach ($grupo_dependencias as $info_dependencia) {
                    $final_info_dependencia[] = $info_dependencia;
                }

                $dependencia_id = $final_info_dependencia[0]['DEPENDENCIA_ID'];
                $jefe_dependencia = UsuarioPeer::getJefeDependencia($dependencia_id);

                /**/
                if ($jefe_dependencia === null) {
                    continue;
                } else {
                    $response_process = self::createExcelReporteSummary($final_info_dependencia, $dependencia_id, $params); //crea el excel
                    //$response2 = self::createExcelReporteFallout($final_info_dependencia, $dependencia_id); //crea el excel
                    $email_params = [];
                    $email_params['ruta_file'] = $response_process['path_absolute'];
                    $email_params['jefe_email'] = trim($jefe_dependencia->getEmail());
                    $email_params['jefe_nombre'] = trim($jefe_dependencia->getNombre());
                    $email_params['jefe_apellido'] = trim($jefe_dependencia->getApellido());
                    $email_params['start_date'] = $params['fecha_inicial'];
                    $email_params['end_date'] = $params['fecha_final'];
                    //***********************************************************************************************
                    $response[] = EstadisticaPeer::sendEmailPending($email_params); //AQUI SE ENVIA EL CORREO
                }
            }
        } catch (PropelException $e) {
            $response_process['message'] = 'Ocurrio un error genrando los datos para el excel';
            return array();
        } catch (\Exception $e) {
            $response_process['message'] = 'Ocurrio un error interno en la aplicacion';
            return array();
        } catch (\Throwable $e) {
            $response_process['message'] = 'Ocurrio un error interno en el servidor';
            return array();
        }
        //***********************************************************************************************************
        return $response;
    }

    private static function createExcelReporteSummary($results, $dependencia_id, $params)
    {
        require_once(sfConfig::get('sf_lib_dir') . "/Spout/Autoloader/autoload.php");
        //***********************************************************************************************************
        try {
            //*******************************************************************************************************
            $writer = WriterEntityFactory::createXLSXWriter();
            //*******************************************************************************************************
            $dir_tmp = ParametroPeer::retrieveByPK(65)->getValortexto();
            $dir_raiz = ParametroPeer::retrieveByPK(9)->getValortexto();
            $path_tmp = $dir_raiz . DIRECTORY_SEPARATOR . $dir_tmp;
            simad_util::createPath($path_tmp);
            //*******************************************************************************************************
            $filename = $dependencia_id . '_' . 'reporte_' . uniqid() . '_' . date('YmdGis') . '.xlsx';
            $tempPath = $path_tmp . DIRECTORY_SEPARATOR . $filename;
            //*******************************************************************************************************
            $writer->openToFile($tempPath);
            //*******************************************************************************************************
            $writer->getCurrentSheet()->setName('Resumen');
            //*******************************************************************************************************
            $headerStyle = (new StyleBuilder())
                ->setFontBold()
                ->setBackgroundColor(Color::rgb(230, 195, 51))
                ->build();
            //*******************************************************************************************************
            $dataStyle = (new StyleBuilder())
                ->setFontSize(12)
                ->build();
            //*******************************************************************************************************
            // Encabezados
            $headers_cols = array(
                'DEPENDENCIA CONSECUTIVO' => 'DEPENDENCIA CONSECUTIVO',
                'USUARIO ASIGNADO' => 'USUARIO ASIGNADO',
                'CANTIDAD DE COMUNICACIONES' => 'CANTIDAD DE COMUNICACIONES',
                'DEPENDENCIA NOMBRE' => 'DEPENDENCIA NOMBRE'
            );
            //*******************************************************************************************************
            $headerRow = WriterEntityFactory::createRowFromArray($headers_cols, $headerStyle);
            $writer->addRow($headerRow);
            //*******************************************************************************************************
            $batchRows = [];
            $count = 0;
            //*******************************************************************************************************
            // Data rows 
            foreach ($results as $rowData) {
                $batchRows[] = WriterEntityFactory::createRowFromArray($rowData, $dataStyle);
                $count++;
                if ($count % 1000 == 0) {
                    $writer->addRows($batchRows);
                    unset($batchRows);
                    $batchRows = [];
                }
            }
            //*******************************************************************************************************
            if (!empty($batchRows)) {
                $writer->addRows($batchRows);
            }
            //*******************************************************************************************************
            $ilist_details = self::getUsersAreaByListDetallado($params,$dependencia_id);
            //*******************************************************************************************************
            $writer->addNewSheetAndMakeItCurrent();
            $writer->getCurrentSheet()->setName('Detalle_Usuario');

            // Encabezados detalle (ajusta columnas)
            $writer->addRow(WriterEntityFactory::createRowFromArray(
                ['RADICADO', 'ASUNTO', 'FECHA_CREACION', 'USUARIO_ASIGNADO', 'DEPENDENCIA_CODIGO', 'DEPENDENCIA_NOMBRE'],
                $headerStyle
            ));
            //*******************************************************************************************************
            foreach ($ilist_details as $drow) {
                $writer->addRow(WriterEntityFactory::createRowFromArray([
                    htmlspecialchars((string)$drow['COM_RADICADO']),
                    htmlspecialchars((string)$drow['COM_ASUNTO']),
                    (string)$drow['COM_FECHA_RADICACION'],
                    htmlspecialchars((string)$drow['USUARIO_ASIGNADO']),
                    htmlspecialchars((string)$drow['DEPENDENCIA_CODIGO']),
                    htmlspecialchars((string)$drow['DEPENDENCIA_NOMBRE']),
                ]));
            }
            //*******************************************************************************************************
            $writer->close();
            //*******************************************************************************************************
            // Verificar que el archivo se creó correctamente
            if (!file_exists($tempPath)) {
                $response_process['message'] = 'Error al generar el archivo Excel';
                return $response_process;
            }
            //*******************************************************************************************************
            $publicUrl = sfConfig::get('publicUrl');
            if (substr($publicUrl, -1) !== '/') {
                $publicUrl .= '/';
            }
            //*******************************************************************************************************
            $response_process['message'] = 'El reporte se genero correctamente';
            $response_process['status'] = 200;
            $response_process['url_download'] = $publicUrl . $dir_tmp . "/" . $filename;
            $response_process['path_absolute'] = $tempPath;
            //*******************************************************************************************************
            return $response_process;
        } catch (PropelException $e) {
            $response_process['message'] = 'Ocurrio un error genrando los datos para el excel';
        } catch (\Exception $e) {
            $response_process['message'] = 'Ocurrio un error interno en la aplicacion';
        } catch (\Throwable $e) {
            $response_process['message'] = 'Ocurrio un error interno en el servidor';
        }
    }

    private static function createExcelReporteDetails($results, $dependencia_id)
    {
        require_once(sfConfig::get('sf_lib_dir') . "/Spout/Autoloader/autoload.php");
        //***********************************************************************************************************
        try {
            //*******************************************************************************************************
            $writer = WriterEntityFactory::createXLSXWriter();
            //*******************************************************************************************************
            $dir_tmp = ParametroPeer::retrieveByPK(65)->getValortexto();
            $dir_raiz = ParametroPeer::retrieveByPK(9)->getValortexto();
            $path_tmp = $dir_raiz . DIRECTORY_SEPARATOR . $dir_tmp;
            simad_util::createPath($path_tmp);
            //*******************************************************************************************************
            $filename = $dependencia_id . '_' . 'reporte_' . uniqid() . '_' . date('YmdGis') . '.xlsx';
            $tempPath = $path_tmp . DIRECTORY_SEPARATOR . $filename;
            //*******************************************************************************************************
            $writer->openToFile($tempPath);
            //*******************************************************************************************************
            $headerStyle = (new StyleBuilder())
                ->setFontBold()
                ->setBackgroundColor(Color::rgb(230, 195, 51))
                ->build();
            //*******************************************************************************************************
            $dataStyle = (new StyleBuilder())
                ->setFontSize(12)
                ->build();
            //*******************************************************************************************************
            // Encabezados
            $headers_cols = array(
                'DEPENDENCIA CONSECUTIVO' => 'DEPENDENCIA CONSECUTIVO',
                'USUARIO ASIGNADO' => 'USUARIO ASIGNADO',
                'CANTIDAD DE COMUNICACIONES' => 'CANTIDAD DE COMUNICACIONES',
                'DEPENDENCIA NOMBRE' => 'DEPENDENCIA NOMBRE'
            );
            //*******************************************************************************************************
            $headerRow = WriterEntityFactory::createRowFromArray($headers_cols, $headerStyle);
            $writer->addRow($headerRow);
            //*******************************************************************************************************
            $batchRows = [];
            $count = 0;
            //*******************************************************************************************************
            // Data rows 
            foreach ($results as $rowData) {
                $batchRows[] = WriterEntityFactory::createRowFromArray($rowData, $dataStyle);
                $count++;
                if ($count % 1000 == 0) {
                    $writer->addRows($batchRows);
                    unset($batchRows);
                    $batchRows = [];
                }
            }
            //*******************************************************************************************************
            if (!empty($batchRows)) {
                $writer->addRows($batchRows);
            }
            //*******************************************************************************************************
            $writer->close();
            //*******************************************************************************************************
            // Verificar que el archivo se creó correctamente
            if (!file_exists($tempPath)) {
                $response_process['message'] = 'Error al generar el archivo Excel';
                return $response_process;
            }
            //*******************************************************************************************************
            $publicUrl = sfConfig::get('publicUrl');
            if (substr($publicUrl, -1) !== '/') {
                $publicUrl .= '/';
            }
            //*******************************************************************************************************
            //EstadisticaPeer::sendEmailPending($periodo_id);
            //*******************************************************************************************************
            $response_process['message'] = 'El reporte se genero correctamente';
            $response_process['status'] = 200;
            $response_process['url_download'] = $publicUrl . $dir_tmp . "/" . $filename;
            $response_process['path_absolute'] = $tempPath;

            return $response_process;
        } catch (PropelException $e) {
            $response_process['message'] = 'Ocurrio un error genrando los datos para el excel';
        } catch (\Exception $e) {
            $response_process['message'] = 'Ocurrio un error interno en la aplicacion';
        } catch (\Throwable $e) {
            $response_process['message'] = 'Ocurrio un error interno en el servidor';
        }
    }

    /**
     * Método genérico para enviar emails usando PHPMailer
     */
    private static function sendEmail($to, $subject, $body, $attachment = array())
    {
        $baseMail = new BaseMailSimad();
        $efectiveMail = "";
        //************************************************************************************
        try {
            
            $baseMail->SetSubject($subject);
            $baseMail->SetMsgHTML($body);
            $baseMail->SetAddAddress(trim($to), trim($to));
            $baseMail->mail_object->isHTML(true);
            $baseMail->mail_object->AltBody = strip_tags($body);
            //********************************************************************************
            if ($attachment != null) {
                foreach ($attachment as $file) {
                    $baseMail->SetAddAttach($file);
                }
            }
            //********************************************************************************
            $efectiveMail = $baseMail->InitSend();
            //********************************************************************************
            if ($efectiveMail === true) {
                $baseMail->writetolog("Alerta enviada comunicaciones pendientes por dependencia: " . $to);
                return true;
            } else {
                $baseMail->writetolog("Error al enviar alerta comunicaciones pendientes por dependencia: " . $to);
                return false;
            }
        } catch (Exception $e) {
            sfContext::getInstance()->getLogger()->err("Error enviando email: {$efectiveMail}");
            return false;
        }
    }

    /**
     * Metodo sendEmailPending(). 
     * Envía el reporte com_recibidas pendientes de responder por dependencia 
     * y lo envia al jefe de dicha dependencia
     * Los parametros de entrada son:
     * - $email_params: array con los siguientes datos:
     *   - jefe_email: correo electronico del jefe de la dependencia
     *   - jefe_nombre: nombre del jefe de la dependencia
     *   - jefe_apellido: apellido del jefe de la dependencia
     *   - ruta_file: ruta del archivo a enviar
     */
    public static function sendEmailPending($email_params)
    {
        try {
            if (!$email_params['jefe_email'] || !trim($email_params['jefe_email'])) {
                sfContext::getInstance()->getLogger()->info('No se puede enviar email: usuario sin email');
                return false;
            }

            $subject = 'SGDEA - Reporte Comunicaciones Recibidas';

            $body = self::getComRecibidaTaskAssignedTemplate([
                'destinatario_nombre' => $email_params['jefe_nombre'],
                'destinatario_apellido' => $email_params['jefe_apellido'],
                'start_date' => $email_params['start_date'],
                'end_date' => $email_params['end_date'],
            ]);

            return self::sendEmail($email_params['jefe_email'], $subject, $body,[$email_params['ruta_file']]);
        } catch (Exception $e) {
            sfContext::getInstance()->getLogger()->err('Error enviando email de tarea asignada: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Template HTML para tarea asignada
     */
    private static function getComRecibidaTaskAssignedTemplate($data)
    {
        $usuario_destino = $data['destinatario_nombre'] . ' ' . $data['destinatario_apellido'];

        $html = '
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background: linear-gradient(135deg, #e6c333 0%, #292c2f 100%); color: white; padding: 30px; text-align: center; border-radius: 8px 8px 0 0; border: 1px solid #e0e0e0; }
                .header h1 { margin: 0; font-size: 24px; color: #333; border-left: 4px solid #af33a1; background: #f8f9fa; border-radius: 4px; }
                .content { background: #fff; padding: 30px; border: 1px solid #e0e0e0; border-top: none; }
                .greeting { font-size: 16px; margin-bottom: 20px; }
                .task-box { background: #f8f9fa; padding: 20px; border-left: 4px solid #af33a1; margin: 20px 0; border-radius: 4px; }
                .task-title { font-size: 18px; font-weight: bold; color: #333; margin-bottom: 15px; }
                .info-row { display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px solid #e0e0e0; }
                .info-label { font-weight: 600; color: #666; }
                .info-value { color: #333; }
                .priority-badge { display: inline-block; padding: 4px 12px; border-radius: 12px; font-size: 12px; font-weight: bold; color: white; }
                .button { display: inline-block; background: #667eea; color: white; padding: 12px 30px; text-decoration: none; border-radius: 5px; margin: 20px 0; font-weight: bold; }
                .button:hover { background: #5568d3; }
                .footer { text-align: center; padding: 20px; color: #999; font-size: 12px; }
            </style>
        </head>
        <body>
            <div class="container">
                <div class="header">
                    <h1>📋 Informe Comunicaciones Recibidas Pendientes</h1>
                </div>
                <div class="content">
                    <div class="greeting">
                        Hola <strong>' . htmlspecialchars($usuario_destino) . '</strong>,
                    </div>
                    <p>Se ha generado un nuevo reporte de comunicaciones recibidas pendientes:</p>
                    
                    <div class="task-box">
                        <div class="task-title">Radicados Pendientes por Gestión</div>
                        
                        <div class="info-row">
                            <span class="info-label">Proceso:</span>
                            <span class="info-value">Gestión de Documentos</span>
                        </div>

                        <div class="info-row">
                            <span class="info-label">Fecha de generación:</span>
                            <span class="info-value">' . date('d/m/Y H:i') . '</span>
                        </div>

                        <div class="info-row">
                            <span class="info-label">Fecha Consulta Registros:</span>
                            <span class="info-value">Desde: <strong>' . $data['start_date'] .'</strong> Hasta: <strong>'. $data['end_date'] . '</strong></span>
                        </div>
                    </div>
                    
                    <p style="margin-top: 20px; font-size: 14px; color: #666;">
                        Por favor, accede al sistema para gestionar las tareas pendientes de su Area.
                    </p>
                </div>
                <div class="footer">
                    Este es un mensaje automático del Sistema de Gestión de Documentos Electrónicos. Por favor no responder a este correo.
                </div>
            </div>
        </body>
        </html>';

        return $html;
    }
}
