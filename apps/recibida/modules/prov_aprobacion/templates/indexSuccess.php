<?php use_helper('jQuery')?>
<?php use_helper('Object')?>

<div class="row">
	<div class="col-md-12">
        <div class="panel panel-primary">
	      <div class="panel-heading">
	        <div class="panel-title">Historico de Aprobacion de Vigencia</div>
	      </div>
	      <div class="panel-body with-table">              
              <table class="table table-bordered table-hover table-striped responsive">
                  <thead>
                      <tr>                            
                          <th>Usuario Aprueba</th>
                          <th>Periodo Validez</th>
                          <th>Estado De Aprobacion</th>
                          <th>Fecha Aprobacion</th>
                          <th>Observaciones</th>        
                      </tr>
                  </thead>
                  <tbody>
                  <?php                   
                  foreach ($prov_aprobacionList as $prov_aprobacion): 
                  ?>
                  <tr>
                      <td><?php echo $usuariosAprobadores[$cont] ?></td>
                      <td><?php echo $prov_aprobacion->getProvPeriodoValidez() ?></td>
                      <td><?php echo $prov_aprobacion->getProvEstadoAprobacion() ?></td>
                      <td><?php echo $prov_aprobacion->getFechaCreacion() ?></td>
                      <td><?php echo $prov_aprobacion->getObservaciones() ?></td> 
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
                    <a class="btn btn-white btn-sm tooltip-primary" href="<?php echo $base_path; ?>/recibida.php/prov_periodo_validez/show?prov_periodo_validez_id=<?php echo $prov_periodo_validez_id ?>" data-toggle="tooltip" data-original-title="Regresar al proveedor"><img border="0" src="<?php echo $base_path ?>/images/simad/ico_cancelar.png" width="25" align="middle"/>Regresar</a>
                    <a class="btn btn-white btn-sm tooltip-primary" href="<?php echo url_for('prov_aprobacion/excel?'.$parametros_consulta) ?>" data-toggle="tooltip" data-original-title="Exportar"><img border="0" src="<?php echo $base_path ?>/images/simad/ico_exportar.png" alt="Exportar" width="25" align="middle"/>Exportar</a>
                </div>
            </div>
            
          </div>
        </div>                      
	</div>
</div>