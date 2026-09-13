<?php use_helper('Object') ?>
<?php echo form_tag('subserie_por_usuario/list',array('name'=>'fin'))?>
<?php use_helper('jQuery')?>
<div class="row">
	<div class="col-md-12">        
        <div class="panel panel-primary">
	      <div class="panel-heading">
	        <div class="panel-title">Subseries Por Usuario</div>
	      </div>
	      <div class="panel-body with-table">              
              <table class="table table-bordered table-hover table-striped responsive">
                  <tbody>
                  <tr>
                    <td class="text-center">
                      <p><?php echo label_for('label1','Los permisos Fueron Eliminados Exitosamente.')?></p>  
                    </td>                    
                </tr>
                 </tbody>
            </table>
          </div>
        </div>
	</div>
</div>