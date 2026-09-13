<?php
use_helper('Object');
use_helper('jQuery');
?>
<div class="row">
	<div class="panel panel-primary" data-collapsed="0">

	  <div class="panel-heading">
	    <div class="panel-title">
	      Adjuntar Archivo
	    </div>
	  </div>

		<div class="panel-body">

			<?php 
			echo form_tag('novedades/uploads', array('name'=>'form1', 'class' => 'form-horizontal form-groups-bordered validate', 'multipart'=> true));
			?>

			<div class="form-group">				
	            <div class="col-sm-4">
	            	<?php if ($sf_request->hasError('file')): ?>
					<?php echo $sf_request->getError('file') ?> 
					<?php endif; ?>  
	              <?php echo input_file_tag('file','',array ('class' => 'form-control'))?>
	            </div>
			</div>
			<div class="clear"></div>

			<div class="form-group">
	          <div class="col-sm-offset-4 col-sm-5">
	            <button type="submit" class="btn btn-success">Adjuntar</button>
	            <!--button type="button" class="btn btn-default" onclick="javascript:parent.jQuery.CloseModalSIMAD();">Cerrar</button-->
	          </div>
	        </div>
	        <div class="clear"></div>

	    	</form>
		</div>
</div>