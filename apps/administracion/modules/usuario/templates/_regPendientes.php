<?php 
$path_theme = substr(sfConfig::get('theme_simad'), -1) == "/" ? sfConfig::get('theme_simad') : sfConfig::get('theme_simad').'/';
$base_path = substr(sfConfig::get('base_simad'), -1) == "/" ? sfConfig::get('base_simad') : sfConfig::get('base_simad').'/';
use_helper('Object','jQuery');

$periodo_id = $el_periodo_id; 
$usuarioEscogido = $usuario->getPrimaryKey();
$process_usuario = UsuarioPeer::getAllProcesoComList();
$totalRegPend = UsuarioPeer::getTotalRegPend($periodo_id, $usuarioEscogido, $process_usuario);
?>


<div class="tabs-vertical-env">
            <ul id="myTab" class="nav nav-tabs bordered" role="tablist">
                <li class="active loadcontent">
                    <a id="pilrub" data-toggle="tab" data-url="<?php echo url_for('/administracion.php/usuario/renderPartialNoVista?periodo_id='.date("Y").'&usuario_id=' . $usuarioEscogido); ?>" href="#<?php echo date("Y"); ?>">
                        <span class="glyphicon glyphicon-list"></span>&nbsp;A&ntilde;o <?php echo date("Y"); ?>
                    </a>
                </li>

                <?php
                    $usuario_id = $usuarioEscogido;
                    $min_periodo = date("Y",strtotime ( '-5 year' , strtotime ( date('Y-m-j') ) ) )  + 1;
                    $periodos_create = PeriodoPeer::getListAllPeriodo();
                    $current_periodo = date("Y");$periodos_list = array();
                    foreach($periodos_create as $year)
                    {
                        if($year['periodo_id'] != $current_periodo && $year['periodo_id'] < $current_periodo)
                        {
                            $url_load = url_for('/administracion.php/usuario/renderPartialNoVista?periodo_id='.$year['periodo_id'].'&usuario_id=' . $usuario_id);
                            echo "<li class='inactive loadcontent'><a id='pilrub' data-toggle='tab' data-url='".$url_load."' href='#".$year['periodo_id']."'><span class='glyphicon glyphicon-list'></span>&nbsp;A&ntilde;o ".$year['text']."</a></li>";
                            $periodos_list[] = $year['periodo_id'];
                        }
                    }
                ?>
            </ul>
            
            <div class="tab-content">
                <div class="tab-pane active" id="<?php echo date("Y"); ?>">
                    <?php 
                        echo include_partial('regPendCont',array('totalRegPend'=>$totalRegPend, 'periodo_id'=>$periodo_id, 'usuario'=>$usuario));
                    ?>
                </div>
            <?php
                foreach($periodos_list as $item)
                {
                    ?>
                <div class="tab-pane " id="<?php echo $item; ?>">
                    <?php 
                    ?>
                </div>
                    <?php
                }
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

        jQuery(href).load(url,function(result)
        {
			javascript:jQuery.CloseLoadingStructData();
            pane.tab('show');
			javascript:jQuery.applyTileStyle();
            jQuery('[data-toggle="tooltip"]').tooltip();
        });
    });
</script>