<?php 
$path_theme = sfConfig::get('theme_simad');
$base_path = sfConfig::get('base_simad');

$dias = array("Domingo","Lunes","Martes","Miercoles","Jueves","Viernes","S&acute;bado");
$meses = array("Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre");

$login_time = $sf_user->getAttribute('login_time', '', 'subscriber');
?>

<script src="<?php print $path_theme;?>assets/js/chartjs/chart.js"></script>
<script src="<?php print $path_theme;?>assets/js/chartjs/chartjs-plugin-datalabels.min.js"></script>
<script src="<?php print $path_theme;?>assets/js/appsgdea-charts.js?v=<?php echo(rand()); ?>"></script>

<style>
    .nav-tabs > li.active > a, .nav-tabs > li.active > a:hover, .nav-tabs > li.active > a:focus {
        color: #504f4e;
        background-color: #FFCD00;
        border-bottom-color: rgb(15, 15, 15);
        border-bottom-color: transparent;
        cursor: pointer;
    }
    .nav-tabs > li.active {
        background-color: #FFCD00;
        border: transparent!important;
    }

    .nav-tabs > li.active {
        border: transparent!important;
    }

    .nav-tabs > li.active > a {
        border: transparent!important;
    }

    .nav-tabs > li.inactive > a {
        border: 1px solid #0073b7!important;
    }

    .nav-tabs > li {
        border: 1px solid #eceaea;
        border-radius: 3px 3px 0 0;
        background-color: #eceaea;
    }

    .nav-tabs > li > a:hover {
        border: transparent!important;
        background-color: #eceaea;
        border: 1px solid red!important;
    }

    .nav-tabs > li.inactive > a:hover {
        border: 1px solid red!important;
    }

    .nav-tabs > li.active > a:hover {
        border: transparent!important;
    }

    /*.nav-tabs { min-width: 600px; }*/
    .tab-content { border: 1px solid gray!important; border-radius: 3px 3px 0 0!important;} 
    .nav-tabs-responsive { overflow: auto; }
</style>

<div class="tabs-vertical-env">
    <ul id="myTab" class="nav nav-tabs bordered" role="tablist">
        <li class="active loadcontent"><a data-toggle="tab" data-url="<?php echo url_for('/backend.php/resumen/dashboard?periodo_id='.date("Y")); ?>" href="#<?php echo date("Y"); ?>"><span class="glyphicon glyphicon-list"></span>&nbsp;A&ntilde;o <?php echo date("Y"); ?></span></a></li>
        <?php
            $min_periodo = date("Y",strtotime ( '-5 year' , strtotime ( date('Y-m-j') ) ) )  + 1;
            $periodos_create = PeriodoPeer::getListAllPeriodo();
            $current_periodo = date("Y");$periodos_list = array();
            foreach($periodos_create as $year)
            {
                if($year['periodo_id'] != $current_periodo && $year['periodo_id'] < $current_periodo)
                {
                    $url_load = url_for('/backend.php/resumen/dashboard?periodo_id='.$year['periodo_id']);
                    echo "<li class='inactive loadcontent'><a data-toggle='tab' data-url='".$url_load."' href='#".$year['periodo_id']."'><span class='glyphicon glyphicon-list'></span>&nbsp;A&ntilde;o ".$year['text']."</a></li>";
                    $periodos_list[] = $year['periodo_id'];
                }
            }
        ?>
    </ul>
    <div class="tab-content">
        <div class="tab-pane active" id="<?php echo date("Y"); ?>">
            <!-- CONTAINER PPAL - INICIO -->
            <div class="row">
                <!-- SIDEBAR BALDOSAS - INICIO -->
                <div class="col-sm-4">
                    <!--INICIO  bloque de baldosas _leftBaldosas.php -->
                    <h2><strong>Mis Actividades Pendientes</strong></h2>

                    <?php
                        echo include_partial('listCurrent',array('total_array_msj' => $total_array_msj, 'recibidas_leer'=>$recibidas_leer,'recibida_copia'=>$recibida_copia,'por_vencer'=>$por_vencer,'vencidas'=>$vencidas ,'usuario'=>$usuario,'periodo_id'=>$periodo_id,
                                'por_distribuir'=>$por_distribuir,'por_gestionar'=>$por_gestionar,'por_ccalidad'=>$por_ccalidad,'workflow_recibida'=>$workflow_recibida,'facturas_recibida'=>$facturas_recibida,
                                'por_leer'=>$por_leer,'copia'=>$copia,'por_responder'=>$por_responder,'internas_revisor'=>$internas_revisor,'internas_firmas'=>$internas_firmas,'internas_prufirmas'=>$internas_prufirmas,
                                'workflow_interna'=>$workflow_interna,'enviadas_revisor'=>$enviadas_revisor,'enviadas_gestor'=>$enviadas_gestor,'enviadas_firmar'=>$enviadas_firmar,'enviadas_purfirmar'=>$enviadas_purfirmar,'enviadas'=>$enviadas,
								'copia_informativa'=>$copia_informativa,'enviadas_gsalida'=>$enviadas_gsalida,'prestamos_pendientes'=>$prestamos_pendientes,'actosadm_data' => $actosadm_data,'periodo_id'=>$periodo_id,'usuario'=>$usuario)); 
                    ?>
                    <!--FIN  bloque de baldosas _leftBaldosas.php -->
                </div>
                <!-- SIDEBAR BALDOSAS - FIN -->
            
                <!-- RIGHT CONTENT - INICIO -->
                <div class="col-sm-8">
                    <!-- INICIO  GRAFICOS _tabGraficas.php  -->
                    <?php 
                        echo include_partial('tabGraficas',array('periodo_id'=>$periodo_id)); 
                    ?>

                    <!--FIN  GRAFICOS _tabGraficas.php -->

                    <!--INICIO  TABLA _tablaDatos.php -->
                    <div class="ensayo">
                        <?php 
                        echo include_partial('tablaDatos',array('periodo_id'=>$periodo_id, 'objects_list' => array())); 
                        ?>
                    </div>
                    <!--FIN  TABLA _tablaDatos.php --> 
                </div>
                <!-- RIGHT CONTENT  - FIN -->
            </div>
            <!-- CONTAINER PPAL - FIN -->
        </div>
        <?php
            foreach($periodos_list as $item)
                echo '<div class="tab-pane" id="'.$item.'"></div>';
        ?>
    </div>
</div>

<script type="text/javascript">
jQuery('#myTab a').click(function (e) 
{
    e.preventDefault();
    javascript:jQuery.LoadingStructData();
    
    var url = jQuery(this).attr("data-url");
    var href = this.hash;
    var pane = jQuery(this);
    // ajax load from data-url
    jQuery(href).load(url,function(result)
    {
        javascript:jQuery.CloseLoadingStructData();
        pane.tab('show');
        //setChartInfoData();
        javascript:jQuery.applyTileStyle();
        jQuery('[data-toggle="tooltip"]').tooltip();
    });
});
</script>