<?php if(count($wf_variables)){ ?>
<!-- variables -->
<div class="row">
	<div class="col-md-12">        
        <?php
	    $cantidad_registros = count($wf_variables);
	    if($cantidad_registros == 0):
	    ?>
        <div class="panel panel-primary">
		      <div class="panel-heading">
		        <div class="panel-title">Variables del workflow</div>
		      </div>
		      <div class="panel-body">
		      	<div class="alert alert-default"><strong>No existen Registros</strong>, Intente con diferentes filtros de consulta.</div>
	      	  </div>
      	</div>        
        <?php
	    else:
	    ?>
        <div class="panel panel-primary">
	      <div class="panel-heading">
	        <div class="panel-title">Variables del workflow</div>
	      </div>
	      <div class="panel-body with-table">              
              <table class="table table-bordered table-hover table-striped responsive">
                  <thead>
                      <tr>
                          <th>Nombre Variable</th>
                          <th>Valor</th>    
                      </tr>
                  </thead>
                  <tbody>
                  <?php foreach ($wf_variables as $wf_variable): ?>
                        <tr>
                            <td><?php echo $wf_variable->getWfVariable()->getNombre(); ?></td>
                            <td><?php echo $wf_variable->getValorVariable(); ?></td>
                        </tr>
                  <?php endforeach; ?>
                  </tbody>
              </table>
          </div>
        </div>                      
    <?php endif; ?>
	</div>
</div>
<!-- Fin mostrar Variables -->
<hr />
<?php } ?>