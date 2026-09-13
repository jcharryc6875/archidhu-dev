<?php use_helper('jQuery')?>
<?php use_helper('Object')?>

<div class="row">
	<div class="col-md-12">
        <div class="panel panel-primary">
	      <div class="panel-heading">
	        <div class="panel-title">Historico de Estados de aprobacion</div>
	      </div>
	      <div class="panel-body with-table">              
              <table class="table table-bordered table-hover table-striped responsive">
                  <thead>
                      <tr>  
                          <th>Item flujo</th>
                          <th>Estado aprobacion</th>
                          <th>Periodo validez</th>
                          <th>Observaciones</th>
                      </tr>
                  </thead>
                  <tbody>
                  <?php                   
                  foreach ($prov_estado_flujo_periodoList as $prov_estado_flujo_periodo):
                  ?>
                  <tr>
                      <td class="text-center">
                        <a href="<?php echo url_for('prov_estado_flujo_periodo/edit?prov_estado_flujo_periodo_id='.$prov_estado_flujo_periodo->getPrimaryKey()) ?>"><?php echo $prov_estado_flujo_periodo->getProvItemFlujo() ?></a>
                      </td>
                      <td><?php echo $prov_estado_flujo_periodo->getProvEstadoAprobacion() ?></td>
                      <td><?php echo $prov_estado_flujo_periodo->getProvPeriodoValidez() ?></td>
                      <td><?php echo $prov_estado_flujo_periodo->getDescripcion() ?></td>
                  </tr>
                  <?php	 
                  endforeach; 
                  ?>
                 </tbody>
            </table>
            <!-- Opciones Listar -->
            <hr />
            <div class="row">
            	<div class="col-sm-12 form-group">                               
                    <a class="btn btn-white btn-sm" href="#" onclick="javascript:parent.jQuery.CloseModalSIMAD();">
                        <img src="<?php echo $base_path ?>/images/simad/ico_cerrar.png" width="25" align="middle"/>Cerrar
                    </a> 
                </div>
            </div>
            
          </div>
        </div>                      
	</div>
</div>