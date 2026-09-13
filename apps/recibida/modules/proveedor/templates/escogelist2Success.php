<?php use_helper('jQuery','Object'); ?>
<div class="row">
	<div class="col-md-12">        
            <div class="panel panel-primary">
    	      <div class="panel-heading">
    	        <div class="panel-title">Proveedor</div>
    	      </div>
    
    	      <div class="panel-body with-table">
              <?php								
                echo form_tag('proveedor/escogelist', array('method'=>'GET','name'=>'form1'));
              ?>
              <table class="table table-bordered table-hover table-striped responsive">
                  <thead>
                      <tr>
                          <th>Nombre</th>
                          <th>Nit</th>
                          <th>Representante legal</th>
                          <th>Identificacion rl</th>
                          <th>Direccion</th>
                          <th>Telefono</th>
                          <th>Naturaleza juridica</th>  
                          
                      </tr>
                  </thead>
                  <tbody>
                  <?php
                 	foreach ($pager->getResults() as $proveedor): 
                  ?>
                  <tr>
                        <td class="text-center">
                        <?php 
                            echo jq_link_to_function($proveedor->getNombre(),'javascript:canClose("'.$proveedor->getNombre().' - '.$proveedor->getRepresentanteLegal().'","'.$proveedor->getPrimaryKey().'","0")'); 
                        ?>
                        </td>
                        <td><?php echo $proveedor->getNit().'&nbsp' ?></td>
                        <td><?php echo $proveedor->getRepresentanteLegal().'&nbsp' ?></td>
                        <td><?php echo $proveedor->getIdentificacionRl().'&nbsp' ?></td>
                        <td><?php echo $proveedor->getDireccion().'&nbsp' ?></td>
                        <td><?php echo $proveedor->getTelefono().'&nbsp' ?></td>
                        <td><?php echo $proveedor->getNaturalezaJuridica().'&nbsp' ?></td> 
                         
                  </tr>
                  <?php
                    endforeach; 
                  ?>
                  </tbody>
              </table>
              <!-- Paginador -->
              <div class="dataTables_wrapper">
                  <div class="row">
                    <div class="col-xs-6 col-left">
                      <div class="dataTables_info" id="table-2_info" role="status" aria-live="polite">Mostrando del <?php print $pager->getFirstIndice();?> al <?php print $pager->getLastIndice(); ?> de <?php print $pager->getNbResults();?></div>
                    </div>
                    <div class="col-xs-6 col-right">
                      <div class="dataTables_paginate paging_bootstrap" id="table-2_paginate">
                        <?php
                        echo use_helper('Pagination');
                        echo pager_navigation($pager, 'proveedor/escogelist', $parametros);
                        ?>
                      </div>
                    </div>
                  </div>
              </div>
            </div>
        </div>              
        <!-- fin  paginacion -->
	</div>
</div>
</form>
<?php echo javascript_tag("         	    
 	function canClose(cad,id,opc) {
 		if(opc == '0'){
 			window.opener.document.form1.$campoText.value = cad;	   
	    	window.opener.document.form1.$campoId.value = id;
	    }
	    if(opc == '1'){	 				   	 		 		 				 						
        	
			window.opener.document.form1.$campoText.value += cad +',';	   
	    	window.opener.document.form1.$campoId.value += id+',';
			
		}		
	    window.close();										    	
	}	
") ?> 