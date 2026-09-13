<?php use_helper('jQuery')?>
<?php use_helper('Object')?>
<?php $currentUser= $sf_user->getAttribute('usuario_id', '', 'subscriber'); ?>
<div class="row">
	<div class="col-md-12">		
            <div class="panel panel-primary">
		      <div class="panel-heading">
		        <div class="panel-title">Lista de chequeo de Documentos del Proveedor</div>
		      </div>
		      <div class="panel-body with-table">		      	
		        <table class="table table-bordered table-hover table-striped responsive">
		          <thead>
		            <tr>
                        <th>Pregunta</th>
                        <th>Prov periodo validez</th>
                        <th>Descripcion</th>
                        <th>Respuesta</th>
                        <th>Observaciones</th>
                        <th>Fecha</th>
                        <th>Modificado_por</th>      
                    </tr>
		          </thead>
		          <tbody>
<?php foreach ($prov_check_list_vigencia_historicoList as $prov_check_list_vigencia_historico): ?>
                    <tr>
                    
        <td class="text-center">
        <?php echo $prov_check_list_vigencia_historico->getProvCheckListVigencia()->getProvCheckListPregunta() ?>
        </td>
        <td><?php echo $prov_check_list_vigencia_historico->getProvCheckListVigencia()->getProvPeriodoValidez() ?></td>
        <td><?php echo $prov_check_list_vigencia_historico->getDescripcion() ?></td>
        <td><?php echo $prov_check_list_vigencia_historico->getRespuesta() ?></td>
        <td><?php echo $prov_check_list_vigencia_historico->getObservaciones() ?></td>
        <td><?php echo $prov_check_list_vigencia_historico->getFechaCreacion() ?></td>
        <td><?php echo $prov_check_list_vigencia_historico->getUsuario() ?></td>
                         
                  </tr>
                  
        <?php endforeach; ?>
                  </tbody>
            </table>
            <hr />
     </div>
    </div>
  </div>
</div>
<?php echo javascript_tag("     
	function openModalDialog(id,accion) {	  	  	 
	  var win = new Window('winShow',{title: '', className: 'alphacube', 
								  bottom:50, left:50, width:800, height:500, 
								  resizable: true, url: accion+''+id, 		   								 
								  showEffectOptions: {duration:1.0} ,wiredDrag: true})
	win.show();
	win.showCenter();
	win.setDestroyOnClose();		
  }
  function openDialog(id,accion) {	  	  	 
	  var win = new Window('show',{title: '', className: 'alphacube', 
								  bottom:50, left:150, width:400, height:350, 
								  resizable: true, url: accion+'?prov_check_list_vigencia_id='+id, 		   								 
								  showEffectOptions: {duration:1.0} ,wiredDrag: true})
	win.show();
	win.setDestroyOnClose();
	win.showCenter();							
  }
  
   function openWindow(accion,titulo) {	  	  	 
	  var win = new Window('dialog',{title: titulo, className: 'alphacube', 
								  bottom:70, left:50, width:500, height:300, 
								  resizable: true, url: accion, showEffectOptions: {duration:1.0}
								  ,wiredDrag: true})
	win.show();
	win.setDestroyOnClose();	
	win.showCenter();					
  }
    
  function canClose() {
  	Windows.getWindow(\"winShow\").close();	
  }  			   	
") ?>

