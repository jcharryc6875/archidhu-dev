<?php

/**
 * resumen actions.
 *
 * @package    simad
 * @subpackage resumen
 * @author     jcharryc - javier.charry@gmail.com
 * @version    SVN: $Id: actions.class.php 2692 2006-11-15 21:03:55Z fabien $
 */

use Box\Spout\Writer\Common\Creator\WriterEntityFactory;

require_once(sfConfig::get('sf_lib_dir')."/fechas.php");

class resumenActions extends sfActions
{
    public function verificaPrilegio($currentForm)
    { 
        $usuariologuiado = $this->getUser()->getAttribute('usuario_id', '', 'subscriber');

        if(!$this->getUser()->checkPerm($currentForm, $usuariologuiado))
        {
            $this->redirect(sfConfig::get('base_simad').'/no_autorizado.html');
        }
    }

    public function executeDashboard()
    {
        $periodo_id = trim($this->getRequestParameter('periodo_id')) ? trim($this->getRequestParameter('periodo_id')) : date("Y");
        $usuariologuiado = $this->getUser()->getAttribute('usuario_id', '', 'subscriber');
		//**********************************************************************************************
		if($usuariologuiado){
            $this->getInfoDashBoard($periodo_id);
        }else{
            return $this->redirect(sfConfig::get('base_simad').'/no_autorizado.html');
        }
        //**********************************************************************************************
        $this->periodo_id = $periodo_id;
    }

    
    public function executeUltimosRadicadosTabla()
    {
        $periodo_id = trim($this->getRequestParameter('periodo_id')) ? trim($this->getRequestParameter('periodo_id')) : date("Y");
        $this->objects_list = $this->getUltimosRadicadosTabla($periodo_id);
        $this->periodo_id = $periodo_id;

        $this->setTemplate('listDatos'); 
    }
    

    public function getUltimosRadicadosTabla($periodo_id = null)
    {
        $usuariologuiado = $this->getUser()->getAttribute('usuario_id', '', 'subscriber');
        //**********************************************Comunicaciones Recibidas Por Leer*******************************************
        $e = new Criteria();

        $e->addJoin(ComRecibidaPeer::COMRECIBIDA_ID, ComrecibidaUsuarioPeer::COMRECIBIDA_ID);
        $e->addJoin(ComrecibidaUsuarioPeer::ESTADOCOMRECIBIDA_ID, EstadoComRecibidaPeer::ESTADOCOMRECIBIDA_ID);
        $e->addJoin(ComrecibidaPeer::TIPOCOMRECIBIDA_ID, TipoComRecibidaPeer::TIPOCOMRECIBIDA_ID);

        $e->add(ComRecibidaPeer::PERIODO_ID, $periodo_id);
        $e->add(ComrecibidaUsuarioPeer::ESTADOCOMRECIBIDA_ID, array(1, 2), Criteria::IN);
        $e->add(ComrecibidaUsuarioPeer::ROLUSUARIORECIBIDAID, 2);
        $e->add(ComRecibidaPeer::IS_LOCKED, 0);
        $e->add(ComrecibidaUsuarioPeer::ESTA_ASIGNADA, 1);

        $e->addSelectColumn(ComRecibidaPeer::RADICADO);
        $e->addSelectColumn(ComRecibidaPeer::ASUNTO);
        $e->addSelectColumn(ComRecibidaPeer::FECHA_CREACION);
        $e->addSelectColumn(TipoComRecibidaPeer::DESCRIPCION);

        $e->addDescendingOrderByColumn(ComRecibidaPeer::FECHA_CREACION);
        $e->setLimit(10);
		//**********************************************************************************************

        return ComRecibidaPeer::doSelectStmt($e)->fetchAll(PDO::FETCH_ASSOC);
    }

    public function executeAreaChart()
    {
        $periodo_id = trim($this->getRequestParameter('periodo_id')) ? trim($this->getRequestParameter('periodo_id')) : date("Y");
        $miResultado = $this->getAreaChart($periodo_id);
        $data_json = json_encode($miResultado);
        //**********************************************************************************************
        $this->getResponse()->setContentType('application/json');      
        return $this->renderText($data_json);
    }

    public function getAreaChart($periodo_id = null)
    {
        $usuariologuiado = $this->getUser()->getAttribute('usuario_id', '', 'subscriber');
        $usuario = UsuarioPeer::retrieveByPk($usuariologuiado);
        $receptor_dep = $usuario->getReceptorDep();
        $dependencia_id = $usuario->getDependenciaId();
        //***********************************************************************************************
        $e = new Criteria();
        $e->addJoin(ComRecibidaPeer::COMRECIBIDA_ID, ComrecibidaUsuarioPeer::COMRECIBIDA_ID);
        $e->addJoin(ComrecibidaUsuarioPeer::ESTADOCOMRECIBIDA_ID, EstadoComRecibidaPeer::ESTADOCOMRECIBIDA_ID);
        $e->add(ComrecibidaUsuarioPeer::ESTADOCOMRECIBIDA_ID, 1);
        $e->add(ComrecibidaUsuarioPeer::ROLUSUARIORECIBIDAID, 2);
        $e->addOr(ComrecibidaUsuarioPeer::ROLUSUARIORECIBIDAID, 3);
        //***********************************************************************************************
        if($receptor_dep == 1)
        { 
            $e->add(ComRecibidaPeer::DEPENDENCIA_ID, $dependencia_id);
            $e->addOr(ComrecibidaUsuarioPeer::USUARIO_ID, $usuariologuiado); 
        }else
        { 
            $e->add(ComrecibidaUsuarioPeer::USUARIO_ID, $usuariologuiado); 
        }
        //***********************************************************************************************
        $e->add(ComRecibidaPeer::IS_LOCKED, 0);
        $e->add(ComrecibidaUsuarioPeer::ESTA_ASIGNADA, 1);
        $e->add(ComRecibidaPeer::PERIODO_ID, $periodo_id);
        //***********************************************************************************************
        $e->clearSelectColumns();
        //***********************************************************************************************
        $e->addSelectColumn(EstadoComRecibidaPeer::DESCRIPCION);
        $e->addSelectColumn('COUNT(*) as TOTAL');
        $e->addSelectColumn("CONVERT(NVARCHAR(7), ". ComRecibidaPeer::TABLE_NAME .".FECHA_CREACION, 120) AS FECHA_REGISTRO");
        $e->addGroupByColumn(EstadoComRecibidaPeer::DESCRIPCION);
        $e->addGroupByColumn("CONVERT(NVARCHAR(7), ". ComRecibidaPeer::TABLE_NAME .".FECHA_CREACION, 120)");
        //***********************************************************************************************
        return ComRecibidaPeer::doSelectStmt($e)->fetchAll(PDO::FETCH_ASSOC);
    }

    public function executeBarStacked()
    {
        $periodo_id = trim($this->getRequestParameter('periodo_id')) ? trim($this->getRequestParameter('periodo_id')) : date("Y");
        $miResultado = $this->getBarStacked($periodo_id);
        $data_json = json_encode($miResultado);
        //**********************************************************************************************
        $this->getResponse()->setContentType('application/json');      
        return $this->renderText($data_json);        
    }

    public function getBarStacked($periodo_id = null)
    {
        $usuariologuiado = $this->getUser()->getAttribute('usuario_id', '', 'subscriber');
        $usuario = UsuarioPeer::retrieveByPk($usuariologuiado);
        //**********************************************************************************************
        $params = [];

        $current_periodo = date("Y");
        if($periodo_id != $current_periodo)
        {
            $params['fecha_inicial'] = "$periodo_id-01-01";
            $params['fecha_final'] = "$periodo_id-12-31";
        }
        else
        {
            $params['fecha_inicial'] = "$current_periodo-01-01";
            $params['fecha_final'] = date('Y-m-d');
        }
        //**********************************************************************************************
        if(!count($params) || !isset($params['fecha_inicial']) || !isset($params['fecha_final']))
        {
            return array();
        }
        else if(empty($params['fecha_inicial']) || empty($params['fecha_final']))
        {
            return array();
        }
        //**********************************************************************************************
        $params['current_user'] = $usuario;
        $params['apply_user_filter'] = True;
        $params['periodo_id'] = $periodo_id;
        //**********************************************************************************************
        try 
        {
            $ilist_data4 = EstadisticaPeer::getDataBarStacked($params);
            $resultado_total = isset($ilist_data4['resultado_total']) ? $ilist_data4['resultado_total'] : null;
            $data_views[] = array('resultado' => $resultado_total, 'tgraph' => 'BarStacked', 'dataviews' => $ilist_data4,'element_chart' => 'barstacked-chart-demo-'.$periodo_id);
            return $data_views;
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

    public function executeLineChart2()
    {
        $periodo_id = trim($this->getRequestParameter('periodo_id')) ? trim($this->getRequestParameter('periodo_id')) : date("Y");
        $miResultado = $this->getLineChart2($periodo_id);
        $data_json = json_encode($miResultado);
        //**********************************************************************************************
        $this->getResponse()->setContentType('application/json');      
        return $this->renderText($data_json);        
    }

    public function getLineChart2($periodo_id = null) // PENDIENTES POR USUARIO
    {
        $usuariologuiado = $this->getUser()->getAttribute('usuario_id', '', 'subscriber');
        $usuario = UsuarioPeer::retrieveByPk($usuariologuiado);
        $receptor_dep = $usuario->getReceptorDep();
        $dependencia_id = $usuario->getDependenciaId();
        //**********************************************************************************************
        $params = [];

        $current_periodo = date("Y");
        if($periodo_id != $current_periodo)
        {
            $params['fecha_inicial'] = "$periodo_id-01-01";
            $params['fecha_final'] = "$periodo_id-12-31";
        }
        else
        {
            $params['fecha_inicial'] = "$current_periodo-01-01";
            $params['fecha_final'] = date('Y-m-d');
        }
        //**********************************************************************************************
        if(!count($params) || !isset($params['fecha_inicial']) || !isset($params['fecha_final']))
        {
            return array();
        }
        else if(empty($params['fecha_inicial']) || empty($params['fecha_final']))
        {
            return array();
        }
        //**********************************************************************************************
        $params['current_user'] = $usuario;
        $params['apply_user_filter'] = True;
        $params['periodo_id'] = $periodo_id;
        //**********************************************************************************************
        try 
        {
            //$ilist_data4 = EstadisticaPeer::getDataBarStacked($params);
            $ilist_data5 = EstadisticaPeer::getDataLineChart2($params);
            $resultado_total = isset($ilist_data5['totales']) ? $ilist_data5['totales'] : null;

            $data_views[] = array(
                'resultado' => $resultado_total, 
                'tgraph' => 'LineChart2', 
                'dataviews' => $ilist_data5,
                'element_chart' => 'line-chart2-'.$periodo_id);
            return $data_views;
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

    public function executeDonutChart2()
    {
        $periodo_id = trim($this->getRequestParameter('periodo_id')) ? trim($this->getRequestParameter('periodo_id')) : date("Y");
        $miResultado = $this->getDonutChart2($periodo_id);

        $data_json = json_encode($miResultado);
        //**********************************************************************************************
        $this->getResponse()->setContentType('application/json');      
        return $this->renderText($data_json);        
    }

    public function getDonutChart2($periodo_id = null) // RECIBIDAS POR TRAMITES.
    {
        $usuariologuiado = $this->getUser()->getAttribute('usuario_id', '', 'subscriber');
        $usuario = UsuarioPeer::retrieveByPk($usuariologuiado);    
        $params = [];
        //**********************************************************************************************
        $current_periodo = date("Y");
        if($periodo_id != $current_periodo){
            $params['fecha_inicial'] = "$periodo_id-01-01";
            $params['fecha_final'] = "$periodo_id-12-31";
        }else{
            $params['fecha_inicial'] = "$current_periodo-01-01";
            $params['fecha_final'] = date('Y-m-d');
        }        
        //**********************************************************************************************
        if(!count($params) || !isset($params['fecha_inicial']) || !isset($params['fecha_final']))
        {
            return array();
        }
        else if(empty($params['fecha_inicial']) || empty($params['fecha_final']))
        {
            return array();
        }
        //**********************************************************************************************
        $params['current_user'] = $usuario;
        $params['apply_user_filter'] = True;
        $params['periodo_id'] = $periodo_id;
        //**********************************************************************************************
        try 
        {
            $tipoGraph = "DonutCustom";
            $resultado = EstadisticaPeer::getComRecibidaPorTramites($params);
            $ilist_data = EstadisticaPeer::getDataDonutByElements($resultado);
            $data_views[] = array('resultado' => $resultado, 'tgraph' => $tipoGraph, 'dataviews' => $ilist_data, 'element_chart' => 'pie-chart-demo-'.$periodo_id);
            return $data_views;
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

    public function executeDonutChart()
    {
        $periodo_id = trim($this->getRequestParameter('periodo_id')) ? trim($this->getRequestParameter('periodo_id')) : date("Y");
        $miResultado = $this->getDonutChart($periodo_id);
		//**********************************************************************************************
        $data_json = json_encode($miResultado);
        //**********************************************************************************************
        $this->getResponse()->setContentType('application/json');      
        return $this->renderText($data_json);        
    }

    public function getDonutChart($periodo_id = null)
    {
        $usuariologuiado = $this->getUser()->getAttribute('usuario_id', '', 'subscriber');
		//**********************************************************************************************
        $e = new Criteria();
        $e->addJoin(ComRecibidaPeer::COMRECIBIDA_ID, ComrecibidaUsuarioPeer::COMRECIBIDA_ID);
        $e->addJoin(ComrecibidaUsuarioPeer::ESTADOCOMRECIBIDA_ID, EstadoComRecibidaPeer::ESTADOCOMRECIBIDA_ID);
        $e->add(ComrecibidaUsuarioPeer::ROLUSUARIORECIBIDAID, 2);
        $e->addOr(ComrecibidaUsuarioPeer::ROLUSUARIORECIBIDAID, 3);
		//**********************************************************************************************
        $e->add(ComrecibidaUsuarioPeer::USUARIO_ID, $usuariologuiado);
        $e->add(ComRecibidaPeer::IS_LOCKED, 0);
        $e->add(ComrecibidaUsuarioPeer::ESTA_ASIGNADA, 1);
        $e->add(ComRecibidaPeer::PERIODO_ID, $periodo_id);
        //**********************************************************************************************
        $e->clearSelectColumns(); 
        $e->addSelectColumn(EstadoComRecibidaPeer::DESCRIPCION);

        $e->addSelectColumn('COUNT(1) as total');
        $e->addGroupByColumn(EstadoComRecibidaPeer::DESCRIPCION);
		//**********************************************************************************************
        return ComRecibidaPeer::doSelectStmt($e)->fetchAll(PDO::FETCH_ASSOC);
    }



    public function executeIndex()
    {    
        $usuariologuiado = $this->getUser()->getAttribute('usuario_id', '', 'subscriber');
        if($usuariologuiado)
        {
            $this->getInfoDashBoard(date("Y"));
        }
        else
        {
            return $this->redirect(sfConfig::get('base_simad').'/no_autorizado.html');
        }
    }
    
	public function executeEstadisticas()
    {    
      
    }
	
	public function executeConsultarEstadisticas() 
    {    
        $currentForm="VER_ESTADISTICAS_COMUNICACIONES";
        $this->verificaPrilegio($currentForm); 

        $mainpanel = '<div class="panel panel-success"><div class="panel-heading"><div class="panel-title">Lista de Resultados de la Consulta</div></div><div class="panel-body"><div class="alert alert-default"><strong>%s</strong>, Favor verificar.</div></div></div>';
		//**************************************************************************************************************
        if(empty($_POST['fechaDocInicial']) || empty($_POST['fechaDocFinal']) || empty($_POST['graphtype']) || empty($_POST['stattype']))
        {
            $error_message = 'DEBE DILIGENCIAR TODOS LOS CAMPOS, codigo: R002';
            $result = sprintf($mainpanel, $error_message);
            exit($result);
        }
		//**************************************************************************************************************
        if (!empty($_POST)) 
        {
            if(!empty($_POST['fechaDocInicial']) && !empty($_POST['fechaDocFinal']) && !empty($_POST['graphtype']) && !empty($_POST['stattype']))
            {
                $stattype = !empty($this->getRequestParameter('stattype')) ? trim($this->getRequestParameter('stattype')) : null;
                $fechaDocInicial = !empty($this->getRequestParameter('fechaDocInicial')) ? trim($this->getRequestParameter('fechaDocInicial')) : null;
                $fechaDocFinal = !empty($this->getRequestParameter('fechaDocFinal')) ? trim($this->getRequestParameter('fechaDocFinal')) : null;
                $tipoGrafica = !empty($this->getRequestParameter('graphtype')) ? trim($this->getRequestParameter('graphtype')) : null; 

                $fechas = array();
                $fechas['inicial'] = $fechaDocInicial;
                $fechas['final'] = $fechaDocFinal;

                if($_POST['stattype'] == 1)
                {
                    $tipo_tabla = 'opcion1';
                    $params = [];
                    $params['fecha_inicial'] = $fechaDocInicial;
                    $params['fecha_final'] = $fechaDocFinal;

                    $resultado = EstadisticaPeer::getComRecibidaPorDependencia($params);

                    //creacion del mega array
                    $data_views = array();

                    if($_POST['graphtype'] == 1)
                    {
                        $tipoGraph = 'BarCustom';
                        $nro_items_y_axis = count($resultado);
                        $caracteres = 'abcdefghijklmnopqrstuvwyz';
                        $array_labels = array();
                        $array_totales = array();
                        $array_ensamble = array('x' => 'Recibidas por tramite');
                        $array_ykeys = array();

                        foreach($resultado as $descripciones)
                        {
                            $foo = $caracteres[rand(0, strlen($caracteres) - 1)];
                            //$array_labels[] = $descripciones['DESCRIPCION'];
                            $array_labels[] = $descripciones['NOMBRE'];
                            $array_totales[] = $descripciones['TOTAL'];
                            $array_ensamble[$foo] = $descripciones['TOTAL'];
                            $array_ykeys[] = $foo; 
                        }

                        $ilist_data['labels'] = $array_labels;
                        $ilist_data['totales'] = $array_totales;
                        $ilist_data['ensamble'] = $array_ensamble;
                        $ilist_data['ykeys'] = $array_ykeys;
                        $ilist_data_bar = $ilist_data;

                        $data_views[] = array('resultado' => $resultado, 'tgraph' => $tipoGraph, 'dataviews' => $ilist_data_bar, 'ttable' => $tipo_tabla);
                    }
                    else
                    {
                        $tipoGraph = 'DonutCustom';

                        $array_labels = array();
                        $array_totales = array();
                        foreach($resultado as $descripciones)
                        {
                            //$array_labels[] = $descripciones['DESCRIPCION'];
                            $array_labels[] = $descripciones['NOMBRE'];
                            $array_totales[] = $descripciones['TOTAL'];                    
                        }
                        $ilist_data['labels'] = $array_labels;
                        $ilist_data['totales'] = $array_totales;
                        $ilist_data_donut = $ilist_data;

                        $data_views[] = array('resultado' => $resultado, 'tgraph' => $tipoGraph, 'dataviews' => $ilist_data_donut, 'ttable' => $tipo_tabla);
                    }

                    $this->las_fechas = $fechas;
                    $this->data_views = $data_views;
                }

                if($_POST['stattype'] == 2)
                {
                    $tipo_tabla = 'opcion2';
                    $params = [];
                    $params['fecha_inicial'] = $fechaDocInicial;
                    $params['fecha_final'] = $fechaDocFinal;

                    $resultado = EstadisticaPeer::getComRecibidaPorTramites($params);

                    //creacion del mega array
                    $data_views = array();

                    if($_POST['graphtype'] == 1)
                    {
                        $tipoGraph = 'BarCustom';
                        $ilist_data = EstadisticaPeer::getDataDonutByElements($resultado);
                        $data_views[] = array('resultado' => $resultado, 'tgraph' => $tipoGraph, 'dataviews' => $ilist_data, 'ttable' => $tipo_tabla);
                    }
                    else
                    {
                        $tipoGraph = 'DonutCustom';

                        $array_labels = array();
                        $array_totales = array();
                        foreach($resultado as $descripciones)
                        {
                            $array_labels[] = $descripciones['DESCRIPCION'];
                            $array_totales[] = $descripciones['TOTAL'];                    
                        }
                        $ilist_data['labels'] = $array_labels;
                        $ilist_data['totales'] = $array_totales;
                        $ilist_data_donut = $ilist_data;

                        $data_views[] = array('resultado' => $resultado, 'tgraph' => $tipoGraph, 'dataviews' => $ilist_data_donut, 'ttable' => $tipo_tabla);
                    }

                    $this->las_fechas = $fechas;
                    $this->data_views = $data_views;
                }

                if($_POST['stattype'] == 3)
                {
                    $tipo_tabla = 'opcion3';
                    $params = [];
                    $params['fecha_inicial'] = $fechaDocInicial;
                    $params['fecha_final'] = $fechaDocFinal;

                    $resultado2 = EstadisticaPeer::getComRecibidaPorMedioRecepcion($params);

                    $array_labels2 = array();
                    $array_totales2 = array();
                    foreach($resultado2 as $descripciones2)
                    {
                        $array_labels2[] = $descripciones2['DESCRIPCION'];
                        $array_totales2[] = $descripciones2['TOTAL'];                    
                    }

                    $ilist_data2['labels'] = $array_labels2;
                    $ilist_data2['totales'] = $array_totales2;
                    
                    $this->ilist_data2 = $ilist_data2;

                    if($_POST['graphtype'] == 1)
                    {
                        $tipoGraph = 'BarCustom';
                    }
                    else
                    {
                        $tipoGraph = 'DonutCustom';
                    }

                    $data_views[] = array('resultado' => $resultado2, 'tgraph' => $tipoGraph, 'dataviews' => $ilist_data2, 'ttable' => $tipo_tabla);

                    $this->las_fechas = $fechas;
                    $this->data_views = $data_views;
                }

                if($_POST['stattype'] == 4)
                {
                    $tipo_tabla = 'opcion4';
                    $params = [];
                    $params['fecha_inicial'] = $fechaDocInicial;
                    $params['fecha_final'] = $fechaDocFinal;

                    $ilist_data4 = EstadisticaPeer::getDataBarStacked($params);
                    $resultado_total = isset($ilist_data4['resultado_total']) ? $ilist_data4['resultado_total'] : null;

                    if($_POST['graphtype'] == 1)
                    {
                        $tipoGraph = 'BarCustom';
                    }
                    else if($_POST['graphtype'] == 2)
                    {
                        $tipoGraph = 'DonutCustom';
                    }
                    else
                    {
                        $tipoGraph = 'BarStacked';
                    }

                    $data_views[] = array('resultado' => $resultado_total, 'tgraph' => $tipoGraph, 'dataviews' => $ilist_data4, 'ttable' => $tipo_tabla);

                    $this->las_fechas = $fechas;
                    $this->data_views = $data_views;
                }

                if($_POST['stattype'] == 5)
                {
                    $tipo_tabla = 'opcion5';
                    $params = [];
                    $params['fecha_inicial'] = $fechaDocInicial;
                    $params['fecha_final'] = $fechaDocFinal;
                    //*************************************************************************************
                    //SIGUIENTE GRAFICO QUINTA CONSULTA
                    $resultado5 = EstadisticaPeer::getComEnviadaGestionadas($params); 
                    $resultado_total = EstadisticaPeer::getAllComEnviadasMes($params);
                    //*************************************************************************************
                    $mas_alto = max(array(count($resultado5), count($resultado_total)));
                    $data_views5 = array();
                    //*************************************************************************************
                    $array_labels5 = array();
                    $array_totales_gestionadas = array();
                    $array_total_radicadas = array();
                    //*************************************************************************************
                    
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
                    if($_POST['graphtype'] == 4)
                    {
                        $tipoGraph = 'LineChart';
                        //*****************************************************************************
                        $ilist_data5['labels'] = $array_labels5;
                        $ilist_data5['total_gestionados'] = $array_totales_gestionadas;
                        $ilist_data5['total_radicadas'] = $array_total_radicadas;

                        $data_views5[] = array('resultado' => $resultado5, 'tgraph' => $tipoGraph, 'dataviews' => $ilist_data5, 'ttable' => $tipo_tabla);
                    }
                    //*************************************************************************************

                    $this->las_fechas = $fechas;
                    $this->data_views = $data_views5;                    
                }

                if($_POST['stattype'] == 6)
                {
                    $tipo_tabla = 'opcion6';
                    $params = [];
                    $params['fecha_inicial'] = $fechaDocInicial;
                    $params['fecha_final'] = $fechaDocFinal;

                    $resultado6 = EstadisticaPeer::getServicioByTipo($params); 

                    $data_views = array();
                    
                    //*************************************************************************************

                    if($_POST['graphtype'] == 1)
                    {
                        $tipoGraph = 'BarCustom';

                        $array_labels = array();
                        $array_totales = array();

                        foreach($resultado6 as $item)
                        {
                            $array_labels[] = $item['VIGENCIA'] . ' ' . $item['MES_TEXT'] . ' ' . $item['NOMBRE_SERVICIO'];
                            $array_totales[] = $item['TOTAL']; 
                        }

                        $ilist_data['labels'] = $array_labels;
                        $ilist_data['totales'] = $array_totales;
                        $ilist_data_bar = $ilist_data;

                        $data_views[] = array('resultado' => $resultado6, 'tgraph' => $tipoGraph, 'dataviews' => $ilist_data_bar, 'ttable' => $tipo_tabla);
                    }
                    else
                    {
                        $tipoGraph = 'DonutCustom';

                        $array_labels = array();
                        $array_totales = array();

                        foreach($resultado6 as $item)
                        {
                            $array_labels[] = $item['VIGENCIA'] . ' ' . $item['MES_TEXT'] . ' ' . $item['NOMBRE_SERVICIO'];
                            $array_totales[] = $item['TOTAL']; 
                        }

                        $ilist_data['labels'] = $array_labels;
                        $ilist_data['totales'] = $array_totales;
                        $ilist_data_donut = $ilist_data; 

                        $data_views[] = array('resultado' => $resultado6, 'tgraph' => $tipoGraph, 'dataviews' => $ilist_data_donut, 'ttable' => $tipo_tabla);
                    }

                    $this->las_fechas = $fechas;
                    $this->data_views = $data_views;
                }

                if($_POST['stattype'] == 7)
                {
                    $params = [];
                    $params['fecha_inicial'] = $fechaDocInicial;
                    $params['fecha_final'] = $fechaDocFinal;

                    $resultado7 = EstadisticaPeer::getServicioByEstado($params); 

                    $data_views = array();
                    $tipo_tabla = 'opcion7';
                    //*************************************************************************************

                    if($_POST['graphtype'] == 1)
                    {
                        $tipoGraph = 'BarCustom';

                        $array_labels = array();
                        $array_totales = array();

                        foreach($resultado7 as $item)
                        {
                            $array_labels[] = $item['VIGENCIA'] . ' ' . $item['MES_TEXT'] . ' ' . $item['ESTADO_SERVICIO'];
                            $array_totales[] = $item['TOTAL']; 
                        }

                        $ilist_data['labels'] = $array_labels;
                        $ilist_data['totales'] = $array_totales;
                        $ilist_data_bar = $ilist_data;

                        $data_views[] = array('resultado' => $resultado7, 'tgraph' => $tipoGraph, 'dataviews' => $ilist_data_bar, 'ttable' => $tipo_tabla);
                    }
                    else
                    {
                        $tipoGraph = 'DonutCustom';

                        $array_labels = array();
                        $array_totales = array();

                        foreach($resultado7 as $item)
                        {
                            $array_labels[] = $item['VIGENCIA'] . ' ' . $item['MES_TEXT'] . ' ' . $item['ESTADO_SERVICIO'];
                            $array_totales[] = $item['TOTAL']; 
                        }

                        $ilist_data['labels'] = $array_labels;
                        $ilist_data['totales'] = $array_totales;
                        $ilist_data_donut = $ilist_data; 

                        $data_views[] = array('resultado' => $resultado7, 'tgraph' => $tipoGraph, 'dataviews' => $ilist_data_donut, 'ttable' => $tipo_tabla);
                    }

                    $this->las_fechas = $fechas;
                    $this->data_views = $data_views;
                }
            }
            else
            {
                $error_message = 'LAS FECHAS DESDE Y HASTA, SON OBLIGATORIAS, codigo: R002';
                $result = sprintf($mainpanel, $error_message);
                exit($result);
            }

        } 
        else 
        {
            $error_message = 'NO EXISTEN DATOS DE ENTRADA, codigo: R001';
            $result = sprintf($mainpanel, $error_message);
            exit($result);
        }
    }
	
	public function executeExcel()
    {
        require_once(sfConfig::get('sf_lib_dir').'/Spout/Autoloader/autoload.php');
        //*******************************************************************************************************
        $opcion = trim($this->getRequestParameter('opcion'));  
        $fechaDocInicial = trim($this->getRequestParameter('f_inicial'));   
        $fechaDocFinal = trim($this->getRequestParameter('f_final'));
        //*******************************************************************************************************
        $params = [];
        $params['fecha_inicial'] = $fechaDocInicial;
        $params['fecha_final'] = $fechaDocFinal;
        //*******************************************************************************************************
        switch($opcion)
        {
            case 1:
                $name_columns = array('DESCRIPCION' => 'DESCRIPCION', 'NOMBRE' => 'NOMBRE', 'TOTAL' => 'TOTAL');
                $resultado = EstadisticaPeer::getComRecibidaPorDependencia($params);
                break;

            case 2:
                $name_columns = array('DESCRIPCION' => 'DESCRIPCION', 'TOTAL' => 'TOTAL');
                $resultado = EstadisticaPeer::getComRecibidaPorTramites($params);
                break;

            case 3:
                $name_columns = array('DESCRIPCION' => 'DESCRIPCION', 'TOTAL' => 'TOTAL');
                $resultado = EstadisticaPeer::getComRecibidaPorMedioRecepcion($params);
                break;

            case 4:
                $name_columns = array('VIGENCIA' => 'VIGENCIA', 'MES' => 'MES', 'CON RESPUESTA' => 'CON_RESPUESTA', 'SIN RESPUESTA' => 'SIN_RESPUESTA', 'TOTAL RADICADOS' => 'TOTAL_RADICADOS');

                $sub_resultado_sinresp = EstadisticaPeer::getComRecibidaSinRespuesta($params); 
                $sub_resultado_total = EstadisticaPeer::getComRecibidaTotales($params); 
                $sub_resultado_conresp = EstadisticaPeer::getComRecibidaConRespuesta($params);

                $resultado = EstadisticaPeer::getSpecialResultadoFinalComRecibidasConRespuesta($sub_resultado_conresp, $sub_resultado_sinresp, $sub_resultado_total);
                break;

            case 5:
                $name_columns = array('VIGENCIA' => 'VIGENCIA', 'MES' => 'MES', 'TOTAL' => 'TOTAL');

                $sub_resultado = EstadisticaPeer::getComEnviadaGestionadas($params); 
                $sub_resultado_total = EstadisticaPeer::getAllComEnviadasMes($params);

                $resultado = EstadisticaPeer::getSpecialResultadoFinalComEnviadasGestionadas($sub_resultado, $sub_resultado_total);
                break;

            case 6:
                $name_columns = array('VIGENCIA' => 'VIGENCIA', 'MES' => 'MES_TEXT', 'TIPO SERVICIO' => 'NOMBRE_SERVICIO', 'TOTAL' => 'TOTAL');
                $resultado = EstadisticaPeer::getServicioByTipo($params);
                break;

            case 7:
                $name_columns = array('VIGENCIA' => 'VIGENCIA', 'MES' => 'MES_TEXT', 'ESTADO SERVICIO' => 'ESTADO_SERVICIO', 'TOTAL' => 'TOTAL');
                $resultado = EstadisticaPeer::getServicioByEstado($params);
                break;
        }
        //*******************************************************************************************************
        $dir_raiz = ParametroPeer::retrieveByPk(9)->getValortexto();
        $dir_tmp = ParametroPeer::retrieveByPk(65)->getValortexto();
        $full_path = simad_util::createPath($dir_raiz . DIRECTORY_SEPARATOR . $dir_tmp);
        $filename = 'data_estadistica_' . uniqid() . '.xlsx';
        //*******************************************************************************************************
        $writer = WriterEntityFactory::createXLSXWriter();
        $writer->openToFile($full_path . DIRECTORY_SEPARATOR . $filename);
        //*******************************************************************************************************
        //creacion de las cabezeras nombre de las columnas
        foreach($name_columns as $clave => $valor)
        {
            $cabezeras[] = WriterEntityFactory::createCell($clave);
        }
        //*******************************************************************************************************
        $encabezados = WriterEntityFactory::createRow($cabezeras);
        $writer->addRow($encabezados);
        //*******************************************************************************************************
        foreach($name_columns as $clave => $valor)
        {
            $filarows[] = $valor; 
        }
        //*******************************************************************************************************
        foreach ($resultado as $fila) 
        {
            $rows = array(); 

            for($i = 0; $i < count($filarows); $i++ )
            {
                $rows[] = $fila[$filarows[$i]];
            }

            //$rows = array( $fila['VIGENCIA'], $fila['MES_TEXT'], $fila['NOMBRE_SERVICIO'], $fila['TOTAL']);
            $writer->addRow(WriterEntityFactory::createRowFromArray($rows));
        }
        //*******************************************************************************************************
        $writer->close();
        //*******************************************************************************************************
        $filesize = filesize($full_path . DIRECTORY_SEPARATOR . $filename);
        //*******************************************************************************************************
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename='.basename($filename));
        header('Content-Transfer-Encoding: binary');
        header('Expires: 0');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Pragma: public');
        header('Content-Length: ' . $filesize);
        ob_clean();
        flush();
        readfile($full_path . DIRECTORY_SEPARATOR . $filename);
        exit;
    }
	
    public function getInfoDashBoard($periodo_id = null)
    {
        $periodo_id = $periodo_id != null ? $periodo_id : date("Y");
        $usuariologuiado = $this->getUser()->getAttribute('usuario_id', '', 'subscriber');
        $this->usuariologuiado = $usuariologuiado;
        $this->usuario = UsuarioPeer::retrieveByPk($usuariologuiado);
        $process_usuario = UsuarioPeer::getUsuarioPocesoComList($usuariologuiado);
        $this->periodo_id = $periodo_id;
        /////////////////////////////////////////////COMUNICACIONES INTERNAS////////////////////////////////////////////////////////
        //****************************************Comunicaciones internas Por Leer**************************************************
        $c = new Criteria();	
        //$c->setDistinct();
        $c->addJoin(ComInternaPeer::COMINTERNA_ID,CominternaUsuarioPeer::COMINTERNA_ID);
        $c->add(CominternaUsuarioPeer::ESTADOCOMINTERNA_ID,2);//por leer
        //$c->addOr(CominternaUsuarioPeer::ESTADOCOMINTERNA_ID,5);//por responder
        $c->add(CominternaUsuarioPeer::USUARIO_ID,$usuariologuiado);    
        $c->add(CominternaUsuarioPeer::ROLUSUARIOCOMINTERNA_ID,4);
        $c->add(ComInternaPeer::PERIODO_ID,$periodo_id);
        $this->por_leer = ComInternaPeer::doCount($c);
        //****************************************Comunicaciones internas por responder*********************************************
        $r = new Criteria();	
        //$c->setDistinct();
        $r->addJoin(ComInternaPeer::COMINTERNA_ID,CominternaUsuarioPeer::COMINTERNA_ID);
        $r->add(CominternaUsuarioPeer::ESTADOCOMINTERNA_ID,5);//por responder
        $r->add(CominternaUsuarioPeer::USUARIO_ID,$usuariologuiado);    
        $r->add(CominternaUsuarioPeer::ROLUSUARIOCOMINTERNA_ID,4);
        $r->add(ComInternaPeer::PERIODO_ID,$periodo_id);
        $this->por_responder = ComInternaPeer::doCount($r);
        //********************************************Comunicaciones Internas Copias************************************************
        $a = new Criteria();
        $a->addJoin(ComInternaPeer::COMINTERNA_ID,CominternaUsuarioPeer::COMINTERNA_ID);
        $a->add(CominternaUsuarioPeer::USUARIO_ID,$usuariologuiado);
        $a->add(ComInternaPeer::PERIODO_ID,$periodo_id);
        $a->add(CominternaUsuarioPeer::ROLUSUARIOCOMINTERNA_ID,3);
        //$a->add(CominternaUsuarioPeer::ESTADOCOMINTERNA_ID,2);
        $this->copia = ComInternaPeer::doCount($a);
        //***************************Workflow Comunicaciones Internas***************************************************************
        /*$wi = new Criteria();
        $wi->setDistinct();
        $wi->addJoin(ComInternaPeer::COMINTERNA_ID,CominternaUsuarioPeer::COMINTERNA_ID);
        $wi->addJoin(ComInternaPeer::INSTANCIA,WfInstanciaPeer::WFINSTANCIA_ID);
        $wi->add(CominternaUsuarioPeer::USUARIO_ID,$usuariologuiado);
        $wi->add(ComInternaPeer::PERIODO_ID,$periodo_id);
        $wi->add(WfInstanciaPeer::ESTA_ABIERTA,1);//workflow ejecutandose
        $wi->add(CominternaUsuarioPeer::WF_EJECUTADO,0);//pendiente por ejecutar
        $wi->add(CominternaUsuarioPeer::ROLUSUARIOCOMINTERNA_ID,4);
        $wi->addOr(CominternaUsuarioPeer::ROLUSUARIOCOMINTERNA_ID,3);
        $this->workflow_interna = ComInternaPeer::doCount($wi);*/
        $this->workflow_interna = null;
        //***********************************************Comunicaciones internas revisor*********************************************
        if(in_array(4,$process_usuario)){//revision
            $rev = new Criteria();    
            $rev->addJoin(ComInternaPeer::COMINTERNA_ID,CominternaUsuarioPeer::COMINTERNA_ID);
            $rev->add(ComInternaPeer::PERIODO_ID,$periodo_id);
            $rev->add(CominternaUsuarioPeer::USUARIO_ID,$usuariologuiado);
            $rev->add(CominternaUsuarioPeer::ESTADOCOMINTERNA_ID,1);
            $rev->add(CominternaUsuarioPeer::ROLUSUARIOCOMINTERNA_ID,5);//revision
            $rev->add(CominternaUsuarioPeer::ESTA_ASIGNADA,1);
            $this->internas_revisor = ComInternaPeer::doCount($rev);
        }else{ $this->internas_revisor = null; }
        //***********************************************Comunicaciones internas firmas*********************************************
        if(in_array(5,$process_usuario)){
            $fir = new Criteria();    
            $fir->addJoin(ComInternaPeer::COMINTERNA_ID,CominternaUsuarioPeer::COMINTERNA_ID);
            $fir->add(ComInternaPeer::PERIODO_ID,$periodo_id);
            $fir->add(CominternaUsuarioPeer::USUARIO_ID,$usuariologuiado);
            $fir->add(CominternaUsuarioPeer::ESTADOCOMINTERNA_ID,1);
            $fir->add(CominternaUsuarioPeer::ROLUSUARIOCOMINTERNA_ID,2);//firmas
            $fir->add(CominternaUsuarioPeer::ESTA_ASIGNADA,1);
            $this->internas_firmas = ComInternaPeer::doCount($fir);
        }else{ $this->internas_firmas = null; }
        //**************************************************************************************************************************
        if(in_array(5,$process_usuario)){
            $fir1 = new Criteria();    
            $fir1->addJoin(ComInternaPeer::COMINTERNA_ID,CominternaUsuarioPeer::COMINTERNA_ID);
            $fir1->add(ComInternaPeer::PERIODO_ID,$periodo_id);
            $fir1->add(ComInternaPeer::PRIORIDADCOM_ID,1);
            $fir1->add(CominternaUsuarioPeer::USUARIO_ID,$usuariologuiado);
            $fir1->add(CominternaUsuarioPeer::ESTADOCOMINTERNA_ID,1);
            $fir1->add(CominternaUsuarioPeer::ROLUSUARIOCOMINTERNA_ID,2);//firmas
            $fir1->add(CominternaUsuarioPeer::ESTA_ASIGNADA,1);
            $this->internas_prufirmas = ComInternaPeer::doCount($fir1);
        }else{ $this->internas_prufirmas = null; }
        /////////////////////////////////////////COMUNICACIONES ENVIADAS/////////////////////////////////////////////////////////////
        //**********************************Comunicaciones Eviadas Copias Informativas***********************************************
        $b = new Criteria();
        $b->addJoin(ComEnviadaPeer::COMENVIADA_ID,EnviadaUsuarioPeer::COMENVIADA_ID);
        $b->add(EnviadaUsuarioPeer::USUARIO_ID,$usuariologuiado);
        $b->add(EnviadaUsuarioPeer::ROLUSCOMENVIADA_ID,3);
        $b->add(EnviadaUsuarioPeer::ESTADOCOMENVIADA_ID,array(4,1),Criteria::NOT_IN);
        $b->add(ComEnviadaPeer::PERIODO_ID,$periodo_id);
        $this->copia_informativa = ComEnviadaPeer::doCount($b);
        //***********************************************Comunicaciones Enviadas*****************************************************
        $d = new Criteria();    
        $d->addJoin(ComEnviadaPeer::COMENVIADA_ID,EnviadaUsuarioPeer::COMENVIADA_ID);
        $d->add(ComEnviadaPeer::PERIODO_ID,$periodo_id);
        $d->add(EnviadaUsuarioPeer::USUARIO_ID,$usuariologuiado);
        $d->add(EnviadaUsuarioPeer::ESTADOCOMENVIADA_ID,array(4,5,1),Criteria::NOT_IN);
        $d->add(EnviadaUsuarioPeer::ROLUSCOMENVIADA_ID,2);	    
        $this->enviadas = ComEnviadaPeer::doCount($d);
        //***********************************************Comunicaciones Enviadas Gestor**********************************************
        if(in_array(3,$process_usuario)){
            $ug = new Criteria();    
            $ug->addJoin(ComEnviadaPeer::COMENVIADA_ID,EnviadaUsuarioPeer::COMENVIADA_ID);
            $ug->add(ComEnviadaPeer::PERIODO_ID,$periodo_id);
            $ug->add(EnviadaUsuarioPeer::USUARIO_ID,$usuariologuiado);
            $ug->add(EnviadaUsuarioPeer::ESTADOCOMENVIADA_ID,1);
            $ug->add(EnviadaUsuarioPeer::ROLUSCOMENVIADA_ID,5);
            $ug->add(EnviadaUsuarioPeer::TIPOPROCESOCOM_ID,3);
            $ug->add(EnviadaUsuarioPeer::ESTA_ASIGNADA,1);
            $this->enviadas_gestor = ComEnviadaPeer::doCount($ug);
        }else{ $this->enviadas_gestor = null; }
        //***********************************************Comunicaciones Enviadas revisor*********************************************
        if(in_array(4,$process_usuario)){
            $ur1 = new Criteria();    
            $ur1->addJoin(ComEnviadaPeer::COMENVIADA_ID,EnviadaUsuarioPeer::COMENVIADA_ID);
            $ur1->add(ComEnviadaPeer::PERIODO_ID,$periodo_id);
            $ur1->add(EnviadaUsuarioPeer::USUARIO_ID,$usuariologuiado);
            $ur1->add(EnviadaUsuarioPeer::ESTADOCOMENVIADA_ID,1);
            $ur1->add(EnviadaUsuarioPeer::ROLUSCOMENVIADA_ID,4);
            $ur1->add(EnviadaUsuarioPeer::TIPOPROCESOCOM_ID,4);
            $ur1->add(EnviadaUsuarioPeer::ESTA_ASIGNADA,1);
            $this->enviadas_revisor = ComEnviadaPeer::doCount($ur1);
        }else{ $this->enviadas_revisor = null; }
        //***********************************************Comunicaciones Enviadas firmas**********************************************
        if(in_array(5,$process_usuario)){
            $df1 = new Criteria();    
            $df1->addJoin(ComEnviadaPeer::COMENVIADA_ID,EnviadaUsuarioPeer::COMENVIADA_ID);
            $df1->add(ComEnviadaPeer::PERIODO_ID,$periodo_id);
            $df1->add(EnviadaUsuarioPeer::USUARIO_ID,$usuariologuiado);
            $df1->add(EnviadaUsuarioPeer::ESTADOCOMENVIADA_ID,1);
            $df1->add(EnviadaUsuarioPeer::ROLUSCOMENVIADA_ID,2);
            $df1->add(EnviadaUsuarioPeer::TIPOPROCESOCOM_ID,5);
            $df1->add(EnviadaUsuarioPeer::ESTA_ASIGNADA,1);
            $this->enviadas_firmar = ComEnviadaPeer::doCount($df1);
        }else{ $this->enviadas_firmar = null; }
        //***************************************************************************************************************************
        if(in_array(5,$process_usuario)){
            $d1 = new Criteria();    
            $d1->addJoin(ComEnviadaPeer::COMENVIADA_ID,EnviadaUsuarioPeer::COMENVIADA_ID);
            $d1->add(ComEnviadaPeer::PERIODO_ID,$periodo_id);
            $d1->add(ComEnviadaPeer::PRIORIDADCOM_ID,1);//Firma Urgente
            $d1->add(EnviadaUsuarioPeer::USUARIO_ID,$usuariologuiado);
            $d1->add(EnviadaUsuarioPeer::ESTADOCOMENVIADA_ID,1);
            $d1->add(EnviadaUsuarioPeer::ROLUSCOMENVIADA_ID,2);
            $d1->add(EnviadaUsuarioPeer::TIPOPROCESOCOM_ID,5);
            $d1->add(EnviadaUsuarioPeer::ESTA_ASIGNADA,1);
            $this->enviadas_purfirmar = ComEnviadaPeer::doCount($d1);
        }else{ $this->enviadas_purfirmar = null; }
        //***********************************************Comunicaciones Enviadas gestor salida***************************************
        $gs = new Criteria();    
        $gs->addJoin(ComEnviadaPeer::COMENVIADA_ID,EnviadaUsuarioPeer::COMENVIADA_ID);
        $gs->add(ComEnviadaPeer::PERIODO_ID,$periodo_id);
        $gs->add(ComEnviadaPeer::SERVICIO_ID,null,Criteria::ISNULL);
        $gs->add(EnviadaUsuarioPeer::ESTADOCOMENVIADA_ID,2);
        $gs->addOr(EnviadaUsuarioPeer::ESTADOCOMENVIADA_ID,3);
        $gs->add(EnviadaUsuarioPeer::ROLUSCOMENVIADA_ID,2);
        if($this->getUser()->checkPerm("COM_ENVIADA_LIST_DEPENDENCIA", $usuariologuiado)){
            $gs->add(ComEnviadaPeer::DEPENDENCIA_ID,$this->usuario->getDependenciaId());
        }else{
            $gs->add(EnviadaUsuarioPeer::USUARIO_ID,$usuariologuiado);
        }
        $this->enviadas_gsalida = ComEnviadaPeer::doCount($gs);
        /////////////////////////////////////////////COMUNICACIONES RECIBIDAS///////////////////////////////////////////////////////
        //**********************************************Comunicaciones Recibidas Por Leer*******************************************
        $e = new Criteria();
        $e->addJoin(ComRecibidaPeer::COMRECIBIDA_ID,ComrecibidaUsuarioPeer::COMRECIBIDA_ID);
        $e->add(ComrecibidaUsuarioPeer::ESTADOCOMRECIBIDA_ID,1);
        $e->add(ComrecibidaUsuarioPeer::ROLUSUARIORECIBIDAID,2);
        $e->addOr(ComrecibidaUsuarioPeer::ROLUSUARIORECIBIDAID,3);
        $e->add(ComrecibidaUsuarioPeer::USUARIO_ID,$usuariologuiado);
        $e->add(ComRecibidaPeer::IS_LOCKED,0);
        $e->add(ComrecibidaUsuarioPeer::ESTA_ASIGNADA,1);
        $e->add(ComRecibidaPeer::PERIODO_ID,$periodo_id);
        $this->recibidas_leer = ComRecibidaPeer::doCount($e);
        //***********************************************Comunicaciones Recibidas Vencidas******************************************
        $f = new Criteria();    
        $f->addJoin(ComRecibidaPeer::COMRECIBIDA_ID,ComrecibidaUsuarioPeer::COMRECIBIDA_ID);
        $f->add(ComrecibidaUsuarioPeer::USUARIO_ID,$usuariologuiado);
        $f->add(ComrecibidaUsuarioPeer::ROLUSUARIORECIBIDAID,2);
        $f->add(ComrecibidaUsuarioPeer::ESTADOCOMRECIBIDA_ID,12,Criteria::NOT_EQUAL);
        $f->add(ComrecibidaUsuarioPeer::ESTADOCOMRECIBIDA_ID,5,Criteria::NOT_EQUAL);
        $f->add(ComRecibidaPeer::IS_LOCKED,0);
        $f->add(ComRecibidaPeer::MARCA_VINCULACION,0);
        $f->add(ComrecibidaUsuarioPeer::ESTA_ASIGNADA,1);
        $f->add(ComRecibidaPeer::PERIODO_ID,$periodo_id);
        $f->add(ComRecibidaPeer::FECHA_MAXIMA_RESPUESTA,date("Y-m-d 23:59:59"),Criteria::LESS_THAN);
        $this->vencidas = ComRecibidaPeer::doCount($f);
        //***********************************************Comunicaciones Recibidas Por Vencer****************************************
        $g = new Criteria();    
        $g->addJoin(ComRecibidaPeer::COMRECIBIDA_ID,ComrecibidaUsuarioPeer::COMRECIBIDA_ID);
        $g->add(ComrecibidaUsuarioPeer::USUARIO_ID,$usuariologuiado);
        $g->add(ComrecibidaUsuarioPeer::ROLUSUARIORECIBIDAID,2);
        $g->add(ComRecibidaPeer::FECHA_MAXIMA_RESPUESTA,$this->getFechaVence(),Criteria::LESS_THAN);	
        $g->addAnd(ComRecibidaPeer::FECHA_MAXIMA_RESPUESTA,date("Y-m-d 00:00:00"),Criteria::GREATER_THAN);
        $g->add(ComRecibidaPeer::IS_LOCKED,0);
        $g->add(ComRecibidaPeer::MARCA_VINCULACION,0);
        $g->add(ComrecibidaUsuarioPeer::ESTA_ASIGNADA,1);
        $g->add(ComRecibidaPeer::PERIODO_ID,$periodo_id);
        $this->por_vencer = ComRecibidaPeer::doCount($g);
        //******************************** Comunicaciones Recibidas Copias**********************************************************
        $h1 = new Criteria();    
        $h1->addJoin(ComRecibidaPeer::COMRECIBIDA_ID,ComrecibidaUsuarioPeer::COMRECIBIDA_ID);
        $h1->add(ComrecibidaUsuarioPeer::USUARIO_ID,$usuariologuiado);
        $h1->add(ComrecibidaUsuarioPeer::ROLUSUARIORECIBIDAID,3);
        $h1->add(ComRecibidaPeer::PERIODO_ID,$periodo_id);
        $h1->add(ComRecibidaPeer::IS_LOCKED,0);
        $this->recibida_copia = ComRecibidaPeer::doCount($h1);
        //******************************** Comunicaciones Recibidas Respuestas******************************************************
        $h2 = new Criteria();    
        $h2->addJoin(ComRecibidaPeer::COMRECIBIDA_ID,ComrecibidaUsuarioPeer::COMRECIBIDA_ID);
        $h2->add(ComrecibidaUsuarioPeer::USUARIO_ID,$usuariologuiado);
        $h2->add(ComrecibidaUsuarioPeer::ROLUSUARIORECIBIDAID,4);
        $h2->add(ComrecibidaUsuarioPeer::ESTADOCOMRECIBIDA_ID,6);
        $h2->add(ComRecibidaPeer::IS_LOCKED,0);
        $h2->add(ComRecibidaPeer::MARCA_VINCULACION,0);
        $h2->add(ComrecibidaUsuarioPeer::ESTA_ASIGNADA,1);
        $h2->add(ComRecibidaPeer::PERIODO_ID,$periodo_id);    
        $this->recibida_responder = ComRecibidaPeer::doCount($h2);
        //******************************** Comunicaciones Recibidas Distribucion****************************************************
        if(in_array(2,$process_usuario)){
            $h3 = new Criteria();
            $h3->addJoin(ComRecibidaPeer::COMRECIBIDA_ID,ComrecibidaUsuarioPeer::COMRECIBIDA_ID);
            $h3->add(ComrecibidaUsuarioPeer::USUARIO_ID,$usuariologuiado);
            $h3->add(ComrecibidaUsuarioPeer::ROLUSUARIORECIBIDAID,2);
            $h3->add(ComRecibidaPeer::TIPOPROCESOCOM_ID,2);
            $h3->add(ComRecibidaPeer::IS_LOCKED,0);
            $h3->add(ComRecibidaPeer::MARCA_VINCULACION,0);
            $h3->add(ComrecibidaUsuarioPeer::ESTA_ASIGNADA,1);
            $h3->add(ComRecibidaPeer::PERIODO_ID,$periodo_id);    
            $this->por_distribuir = ComRecibidaPeer::doCount($h3);
        }else{ $this->por_distribuir = null; }
        //******************************** Comunicaciones Recibidas Gestor**********************************************************
        if(in_array(3,$process_usuario)){
            $h4 = new Criteria();    
            $h4->addJoin(ComRecibidaPeer::COMRECIBIDA_ID,ComrecibidaUsuarioPeer::COMRECIBIDA_ID);
            $h4->add(ComrecibidaUsuarioPeer::USUARIO_ID,$usuariologuiado);
            $h4->add(ComrecibidaUsuarioPeer::ROLUSUARIORECIBIDAID,2);
            $h4->add(ComRecibidaPeer::TIPOPROCESOCOM_ID,3);
            $h4->add(ComRecibidaPeer::IS_LOCKED,0);
            $h4->add(ComRecibidaPeer::MARCA_VINCULACION,0);
            $h4->add(ComrecibidaUsuarioPeer::ESTA_ASIGNADA,1);
            $h4->add(ComRecibidaPeer::PERIODO_ID,$periodo_id);
            $h4->add(ComrecibidaUsuarioPeer::ESTADOCOMRECIBIDA_ID,5,Criteria::NOT_EQUAL);
            $this->por_gestionar = ComRecibidaPeer::doCount($h4);
        }else{ $this->por_gestionar = null; }
        //******************************** Comunicaciones Recibidas Control Calidad*************************************************
        if(in_array(6,$process_usuario)){
            $h5 = new Criteria();    
            $h5->addJoin(ComRecibidaPeer::COMRECIBIDA_ID,ComrecibidaUsuarioPeer::COMRECIBIDA_ID);
            $h5->add(ComrecibidaUsuarioPeer::USUARIO_ID,$usuariologuiado);
            $h5->add(ComrecibidaUsuarioPeer::ROLUSUARIORECIBIDAID,2);
            $h5->add(ComRecibidaPeer::TIPOPROCESOCOM_ID,6);
            $h5->add(ComRecibidaPeer::IS_LOCKED,0);
            $h5->add(ComrecibidaUsuarioPeer::ESTA_ASIGNADA,1);
            $h5->add(ComRecibidaPeer::PERIODO_ID,$periodo_id);    
            $this->por_ccalidad = ComRecibidaPeer::doCount($h5);
        }else{ $this->por_ccalidad = null; }
        //*********************************Workflows Recibidas**********************************************************************
        $wo = new Criteria();
        $wo->setDistinct();
        $wo->addJoin(ComRecibidaPeer::COMRECIBIDA_ID,ComrecibidaUsuarioPeer::COMRECIBIDA_ID);
        $wo->addJoin(ComRecibidaPeer::INSTANCIA,WfInstanciaPeer::WFINSTANCIA_ID);
        $wo->add(ComrecibidaUsuarioPeer::ESTADOCOMRECIBIDA_ID,14,Criteria::NOT_EQUAL);
        $wo->addAnd(ComrecibidaUsuarioPeer::ESTADOCOMRECIBIDA_ID,12,Criteria::NOT_EQUAL);
        $wo->add(ComrecibidaUsuarioPeer::ROLUSUARIORECIBIDAID,2);
        $wo->addOr(ComrecibidaUsuarioPeer::ROLUSUARIORECIBIDAID,3);
        $wo->add(ComrecibidaUsuarioPeer::USUARIO_ID,$usuariologuiado);        
        $wo->add(ComRecibidaPeer::PERIODO_ID,$periodo_id);
        $wo->add(WfInstanciaPeer::ESTA_ABIERTA,1);//workflow ejecutandose
        $wo->add(ComrecibidaUsuarioPeer::WF_EJECUTADO,0);//pendiente por ejecutar
        $this->workflow_recibida = ComRecibidaPeer::doCount($wo);
        //*********************************Facturas Recibidas***********************************************************************
        $fac = new Criteria();
        $fac->setDistinct();
        $fac->addJoin(FacturaPeer::FACTURA_ID,FacturaVitacoraPeer::FACTURA_ID);
        $fac->addJoin(FacturaVitacoraPeer::FACTURAVITACORA_ID,FactUsuarioDestinoPeer::FACTURAVITACORA_ID);
        $fac->add(FacturaVitacoraPeer::EJECUTADA,0);
        $fac->add(FactUsuarioDestinoPeer::FACTURAVITACORAROLUSUARIO_ID,2);
        $fac->add(FactUsuarioDestinoPeer::USUARIO_ID,$usuariologuiado);
        //$fac->add(FacturaPeer::PERIODO_ID,$periodo_id);        
        $this->facturas_recibida = FacturaPeer::doCount($fac);
        /////////////////////////////////////////////ARCHIVO////////////////////////////////////////////////////////////////////////
        //*********************************Archivo Prestamos************************************************************************
        $apr = new Criteria();
        $apr->setDistinct();
        $apr->addJoin(PrestamoPeer::PRESTAMO_ID,DetallePrestamoPeer::PRESTAMO_ID);
        $apr->add(PrestamoPeer::ESTADOPRESTAMO_ID,1);
        $apr->add(PrestamoPeer::USUARIO_ID,$usuariologuiado);
        $this->prestamos_pendientes = PrestamoPeer::doCount($apr);
        //**************************************************************************************************************************
        /////////////////////////////////////////////ACTOS ADMINISTRATIVOS//////////////////////////////////////////////////////////
        //**************************************************************************************************************************
        $ad0 = new Criteria();
        $ad0->addJoin(ActoAdministrativoPeer::ACTOADMINISTRATIVO_ID,ActoadministrativoUsuarioPeer::ACTOADMINISTRATIVO_ID);
        $ad0->add(ActoadministrativoUsuarioPeer::ESTADOACTOADMINISTRATIVO_ID,6,Criteria::EQUAL);
        $ad0->add(ActoadministrativoUsuarioPeer::ROLUSUARIOACTOADMINISTVO_ID,6);
        $ad0->add(ActoadministrativoUsuarioPeer::USUARIO_ID,$usuariologuiado);
        $ad0->add(ActoAdministrativoPeer::PERIODO_ID,$periodo_id);
        $actosadm_data['actoadm_leer'] = ActoAdministrativoPeer::doCount($ad0);
        //**********************************************************En revision******************************************************
        if(in_array(4,$process_usuario)){
            $ad1 = new Criteria();    
            $ad1->addJoin(ActoAdministrativoPeer::ACTOADMINISTRATIVO_ID,ActoadministrativoUsuarioPeer::ACTOADMINISTRATIVO_ID);
            $ad1->add(ActoAdministrativoPeer::PERIODO_ID,$periodo_id);
            $ad1->add(ActoadministrativoUsuarioPeer::USUARIO_ID,$usuariologuiado);
            $ad1->add(ActoadministrativoUsuarioPeer::ESTADOACTOADMINISTRATIVO_ID,array(1,3),Criteria::IN);//enviado a revision
            $ad1->add(ActoadministrativoUsuarioPeer::ROLUSUARIOACTOADMINISTVO_ID,3);
            $ad1->add(ActoadministrativoUsuarioPeer::TIPOPROCESOCOM_ID,4);
            $ad1->add(ActoadministrativoUsuarioPeer::ESTA_ASIGNADA,1);
            $actosadm_data['actoadm_revisor'] = ActoAdministrativoPeer::doCount($ad1);
        }else{ $actosadm_data['actoadm_revisor'] = null; }
        //******************************************************Enviadas firmas*****************************************************
        if(in_array(5,$process_usuario)){
            $ad2 = new Criteria();    
            $ad2->addJoin(ActoAdministrativoPeer::ACTOADMINISTRATIVO_ID,ActoadministrativoUsuarioPeer::ACTOADMINISTRATIVO_ID);
            $ad2->add(ActoAdministrativoPeer::PERIODO_ID,$periodo_id);
            $ad2->add(ActoadministrativoUsuarioPeer::USUARIO_ID,$usuariologuiado);
            $ad2->add(ActoadministrativoUsuarioPeer::ESTADOACTOADMINISTRATIVO_ID,array(1,4),Criteria::IN);//enviado firma
            $ad2->add(ActoadministrativoUsuarioPeer::ROLUSUARIOACTOADMINISTVO_ID,2);//firma
            $ad2->add(ActoadministrativoUsuarioPeer::TIPOPROCESOCOM_ID,5);
            $ad2->add(ActoadministrativoUsuarioPeer::ESTA_ASIGNADA,1);
            $actosadm_data['actoadm_firmar'] = ActoadministrativoUsuarioPeer::doCount($ad2);
        }else{ $actosadm_data['actoadm_firmar'] = null; }
        //***************************************************************************************************************************
        if(in_array(5,$process_usuario)){
            $ad3 = new Criteria();    
            $ad3->addJoin(ActoAdministrativoPeer::ACTOADMINISTRATIVO_ID,ActoadministrativoUsuarioPeer::ACTOADMINISTRATIVO_ID);
            $ad3->add(ActoAdministrativoPeer::PERIODO_ID,$periodo_id);
            $ad3->add(ActoAdministrativoPeer::PRIORIDADCOM_ID,1);//Firma Urgente
            $ad3->add(ActoadministrativoUsuarioPeer::USUARIO_ID,$usuariologuiado);
            $ad3->add(ActoadministrativoUsuarioPeer::ESTADOACTOADMINISTRATIVO_ID,array(1,4),Criteria::IN);//enviado firma
            $ad3->add(ActoadministrativoUsuarioPeer::ROLUSUARIOACTOADMINISTVO_ID,2);//firma
            $ad3->add(ActoadministrativoUsuarioPeer::TIPOPROCESOCOM_ID,5);
            $ad3->add(ActoadministrativoUsuarioPeer::ESTA_ASIGNADA,1);
            $actosadm_data['actoadm_purfirmar'] = ActoadministrativoUsuarioPeer::doCount($ad3);
        }else{ $actosadm_data['actoadm_purfirmar'] = null; }
        //************************************************* Enviada a Gestor**********************************************************
        if(in_array(3,$process_usuario)){
            $ad4 = new Criteria();    
            $ad4->addJoin(ActoAdministrativoPeer::ACTOADMINISTRATIVO_ID,ActoadministrativoUsuarioPeer::ACTOADMINISTRATIVO_ID);
            $ad4->add(ActoAdministrativoPeer::PERIODO_ID,$periodo_id);
            $ad4->add(ActoadministrativoUsuarioPeer::USUARIO_ID,$usuariologuiado);
            $ad4->add(ActoadministrativoUsuarioPeer::ESTADOACTOADMINISTRATIVO_ID,array(1,2),Criteria::IN);//enviado a gestor
            $ad4->add(ActoadministrativoUsuarioPeer::ROLUSUARIOACTOADMINISTVO_ID,4);//gestor
            $ad4->add(ActoadministrativoUsuarioPeer::TIPOPROCESOCOM_ID,3);
            $ad4->add(ActoadministrativoUsuarioPeer::ESTA_ASIGNADA,1);
            $actosadm_data['actoadm_gestionar'] = ActoAdministrativoPeer::doCount($ad4);
        }else{ $actosadm_data['actoadm_gestionar'] = null; }
        //***********************************************en gestion salida************************************************************
        $adgs = new Criteria();
        $adgs->setDistinct();

        $adgs->addJoin(ActoAdministrativoPeer::ACTOADMINISTRATIVO_ID,ActoadministrativoUsuarioPeer::ACTOADMINISTRATIVO_ID,Criteria::INNER_JOIN);
        $adgs->addJoin(ActoAdministrativoPeer::ACTOADMINISTRATIVO_ID,ActoadministraInteresadoPeer::ACTOADMINISTRATIVO_ID,Criteria::INNER_JOIN);
        $adgs->addJoin(ActoAdministrativoPeer::ACTOADMINISTRATIVO_ID,ActoadministrativoServicioPeer::ACTOADMINISTRATIVO_ID,Criteria::LEFT_JOIN);

        $adgs->add(ActoAdministrativoPeer::PERIODO_ID, $periodo_id);
        $adgs->add(ActoadministrativoUsuarioPeer::ESTADOACTOADMINISTRATIVO_ID,array(6,7,8,9),Criteria::IN);
        $adgs->add(ActoadministrativoUsuarioPeer::ROLUSUARIOACTOADMINISTVO_ID,2);//firmante
        $adgs->add(ActoadministrativoServicioPeer::ACTOADMINISTRATIVO_ID,null,Criteria::ISNULL);

        if($this->getUser()->checkPerm("ACTO_ADMINISTRATIVO_LIST_DEPENDENCIA", $usuariologuiado)){
            $adgs->add(ActoAdministrativoPeer::DEPENDENCIA_ID,$this->usuario->getDependenciaId());
        }else{
            $adgs->add(ActoadministrativoUsuarioPeer::USUARIO_ID,$usuariologuiado);
        }
        $actosadm_data['actoadm_gsalida'] = ActoAdministrativoPeer::doCount($adgs);
        //*********************************************************Firmados************************************************************
        $adfs = new Criteria();    
        $adfs->addJoin(ActoAdministrativoPeer::ACTOADMINISTRATIVO_ID,ActoadministrativoUsuarioPeer::ACTOADMINISTRATIVO_ID);
        $adfs->add(ActoAdministrativoPeer::PERIODO_ID,$periodo_id);
        $adfs->add(ActoadministrativoUsuarioPeer::ESTADOACTOADMINISTRATIVO_ID,array(6,7,8,9),Criteria::IN);
        $adfs->add(ActoadministrativoUsuarioPeer::ROLUSUARIOACTOADMINISTVO_ID,2);//firmante
        $adfs->add(ActoadministrativoUsuarioPeer::USUARIO_ID,$usuariologuiado);
        $actosadm_data['actoadm_firmadas'] = ActoAdministrativoPeer::doCount($adfs);
        //****************************************************************************************************************************
        $admco = new Criteria();
        $admco->addJoin(ActoAdministrativoPeer::ACTOADMINISTRATIVO_ID,ActoadministrativoUsuarioPeer::ACTOADMINISTRATIVO_ID);
        $admco->add(ActoadministrativoUsuarioPeer::USUARIO_ID,$usuariologuiado);
        $admco->add(ActoadministrativoUsuarioPeer::ROLUSUARIOACTOADMINISTVO_ID,7);
        $admco->add(ActoadministrativoUsuarioPeer::ESTADOACTOADMINISTRATIVO_ID,array(6,7,8,9),Criteria::IN);
        $admco->add(ActoAdministrativoPeer::PERIODO_ID,$periodo_id);
        $actosadm_data['actoadm_infcopia'] = ActoAdministrativoPeer::doCount($admco);
        //****************************************************************************************************************************
        $this->actosadm_data = $actosadm_data;
        ////////////////////////////////////////////////////// MENSAJERIA ////////////////////////////////////////////////////////////
        //*********************************MENSAJERIA*********************************************************************************
        $list_items = simad_util::readConfigFileApp(array('mensajeria_items'));
        $servicios_current = [];
        $total_conteo = 0;
        foreach($list_items['mensajeria_items'] as $value)
        {
            $gen_msj = new Criteria();
            $gen_msj->addJoin(ServicioPeer::SERVICIO_ID, AsignarServicioPeer::SERVICIO_ID, Criteria::INNER_JOIN);
            $gen_msj->addJoin(AsignarServicioPeer::ASIGNARSERVICIO_ID, UsuarioAsignadoSolicitudPeer::ASIGNARSERVICIO_ID, Criteria::INNER_JOIN);
            $gen_msj->add(ServicioPeer::TIPOSERVICIO_ID, $value);
            $gen_msj->add(ServicioPeer::SERVICIOESTADO_ID, array(1, 2, 4), CRITERIA::IN);
            $gen_msj->add(UsuarioAsignadoSolicitudPeer::USUARIO_ID, $usuariologuiado);
            $gen_msj->add(UsuarioAsignadoSolicitudPeer::ESTA_ASIGNADO, 1);
            $gen_msj->add(UsuarioAsignadoSolicitudPeer::ROLUSUARIOASIGNACIONSERVICIO_ID, 2);

            $conteo = ServicioPeer::doCount($gen_msj);

            if ($conteo > 0) {
                $tdescripcion = TipoServicioPeer::retrieveByPk($value)->getDescripcion();
                //******************************************************************************************************************
                $servicios_current[] = ['count' => $conteo, 'tiposervicio_id' => $value, 'descripcion' => $tdescripcion];
                $total_conteo += $conteo;
            }
        }
        //**************************************************************************************************************************
        $total_array_msj['total_conteo'] = $total_conteo > 0 ? true : false;
        $total_array_msj['data'] = $servicios_current;
        $this->total_array_msj = $total_array_msj;
        //****************************************************************************************************************************
        $image_entidad = trim($this->usuario->getRegional()->getEntidad()->getLogoCorporativo());
        $base_image = "/images";
        $this->licencia_image = $image_entidad ? $base_image."/encabezado_carta/logos_carnet/".$image_entidad : $base_image."/lincenciadoa.jpg";
    }

    public function getFechaVence()
    {
        $dias = 3;
        $fecha   = AddDays(date("Y-m-d"),$dias);		        
        return $fecha;
    }
}
?>