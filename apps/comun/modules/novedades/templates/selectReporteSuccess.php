<?php 
$path_theme = sfConfig::get('theme_simad');
$base_path = sfConfig::get('base_simad');

use_helper('Object','jQuery'); 
?>
<div class="row">
  <div class="col-md-12">
    <div class="panel panel-primary">
      <div class="panel-heading">
        <div class="panel-title">Tipos De Reporte Novedades</div>
      </div>
      <div class="panel-body with-table">
        <table class="table table-bordered table-hover table-striped table-condensed responsive">
          <thead>
            <tr>
              <th>Tipo Reporte</th>
              <th>Opciones</th>
            </tr>
          </thead>
          <tbody>           
            <tr>
              <td>Control Correspondencia Sin Radicar</td>
              <td class="text-center">
                <a href="<?php echo $base_path; ?>/comun.php/novedades/reporte?<?php echo $parametros?>&tipo_reporte=1" target="_new">
                    <?php echo image_tag($base_path.'/images/simad/ico_exportar.png',array('border'=>"0",'width'=>"25")) ?>
                </a>
              </td>
            </tr>
           <tr>
              <td>Control Facturas Recibidas</td>
              <td class="text-center">
                <a href="<?php echo $base_path; ?>/comun.php/novedades/reporte?<?php echo $parametros?>&tipo_reporte=2" target="_new">
                    <?php echo image_tag($base_path.'/images/simad/ico_exportar.png',array('border'=>"0",'width'=>"25")) ?>
                </a>
              </td>
            </tr>
            <tr>
              <td>Control Entrega Facturas Mayoristas</td>
              <td class="text-center">
                <a href="<?php echo $base_path; ?>/comun.php/novedades/reporte?<?php echo $parametros?>&tipo_reporte=3" target="_new">
                    <?php echo image_tag($base_path.'/images/simad/ico_exportar.png',array('border'=>"0",'width'=>"25")) ?>
                </a>
              </td>
            </tr>            
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>