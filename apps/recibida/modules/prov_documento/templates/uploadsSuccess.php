<?php use_helper('Object') ?>
<?php use_helper('Date') ?>
<?php use_helper('jQuery')?>

<div class="row">
  <div class="col-md-12">
    <!-- Contenedor Pagina -->
    <div class="panel panel-gradient" data-collapsed="0">
      <div class="panel-heading">
        <div class="panel-title">
          Escoger Archivo
        </div>
      </div>
      <!-- Contenedor Contenido Formulario-->
      <div class="panel-body">
      <?php 
        echo form_tag('prov_documento/uploads', array('name'=>'fin', 'multipart'=> true, 'role' => 'form', 'class' => 'form-horizontal form-groups-bordered validate'));
      ?>
       <div class="form-group">
          <!-- adjuntos -->          
          <div class="col-sm-2">
            <!--div class="input-group"-->
              <?php 
                echo textarea_tag('cadena' ,$fileName, array ('readonly'=>'true', 'class'=>'form-control input-sm',));
              ?>              
            <!--/div-->
          </div>          
      </div>
      <div class="form-group">
        <!-- Botonera -->
        <div class="col-sm-offset-4 col-sm-5">
            <button type="button" class="btn btn-default btn-sm" onclick="javascript:seleccionar();"><i class="entypo-cancel-circled"></i>Finalizar</button>
        </div>
      </div>
      <div class="clear"></div>
      </form>
      </div>
    </div>
  </div>
</div>
<?php echo javascript_tag('
   function seleccionar() {
        var val = document.getElementById("cadena").value;
        if(val != ""){
   		   parent.document.form1.ruta.value += val + ",";
           parent.jQuery.CloseModalSIMAD();
   		}else{
   		   alert("Debe adjuntar un archivo!!");
   		}   		
	}            
') ?>