<?php 
$path_theme = sfConfig::get('theme_simad');
$base_path = sfConfig::get('base_simad');

use_helper('jQuery');
?>
<div class="panel panel-gradient" data-collapsed="0">
  <div class="panel-heading">
    <div class="panel-title">Periodos de Validez</div>
  </div>
  <div class="panel-body with-table">              
      <table class="table table-bordered table-hover table-striped responsive">
          <thead>
              <tr>
                  <th>Fecha inicial</th>
                  <th>Fecha final</th>
                  <th>Revision Creacion</th>
                  <th>Opciones</th>
              </tr>
          </thead>
          <tbody>
          <?php                    
          foreach ($prov_periodo_validezList as $prov_periodo_validez):
          ?>
          <tr>
              <td class="text-center">
              <?php if($sf_user->checkPerm("PROV_VER_PERIODOS_VALIDES_ANULADOS", $currentUser) || $prov_periodo_validez->getProveedor()->getProvEstadoId()!=6):?>
                        <a href="<?php echo url_for('prov_periodo_validez/show?prov_periodo_validez_id='.$prov_periodo_validez->getProvPeriodoValidezId()) ?>"><?php echo $prov_periodo_validez->getFechaInicial() ?></a>                            
               <?php 
                    else:
                        echo $prov_periodo_validez->getFechaInicial();
                    endif;
               ?>
              </td>
              
              <td><?php echo $prov_periodo_validez->getFechaFinal() ?></td>
              <td><?php echo $prov_periodo_validez->getProvRevisadoCreacion() ?></td>
              <td class="text-center"><?php echo link_to(image_tag("/images/simad/ico_ejecutar.png", array('border'=>"0",'width'=>"30",'align'=>"middle","title"=>'Ir al periodo de validez',)), 'prov_periodo_validez/show?prov_periodo_validez_id='.$prov_periodo_validez->getProvPeriodoValidezId());?></td>
          </tr>
          <?php	 
          endforeach; 
          ?>
         </tbody>
    </table>
  </div>
</div>