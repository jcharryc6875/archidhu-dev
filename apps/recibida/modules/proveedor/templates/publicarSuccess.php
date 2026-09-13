<?php use_helper('Object','jQuery') ?>

<div class="row">
  <div class="col-md-12">
    <?php
	// Include NavbarArchivo
	include_once("_navbar_proveedor.php");        
	?>
    <!-- Contenedor Pagina -->
    <div class="panel panel-gradient" data-collapsed="0">
      <div class="panel-heading">
        <div class="panel-title">
          Publicar Proveedor
        </div>
      </div>
      <!-- Contenedor Contenido Formulario-->
      <div class="panel-body">
        <?php 
        echo form_tag('proveedor/updatePublicar', array('name'=>'form1','role' => 'form', 'class' => 'form-groups-bordered validate'));
        echo object_input_hidden_tag($proveedor, 'getProveedorId');
        ?>
		<div class="row">
			<div class="col-sm-6">
				<div class="form-group">
					<!-- Ciudad -->
					<label for="lbCiudadId" class="control-label">Ciudad:</label>
					<?php 
					   echo object_select_tag($directorio_externo, 'getCiudadId', array ('peer_method'=>'getAllCiudad','related_class' => 'Ciudad','class'=>'form-control input-sm required','include_custom'=>'Seleccione...',));
					?>
				</div>
			</div>
			<div class="col-sm-6">
				<div class="form-group">
					<!-- Nombre -->
					<label for="lbConsecutivo" class="control-label">Cargo Funcionario:</label>
					<?php
						echo input_tag('cargo_funcionario', '', array('class'=>'form-control input-sm'));
					?>
				</div>
			</div>
		</div>

		<div class="form-group">
			<!-- Botonera -->
			<div class="col-sm-offset-4 col-sm-5">
				<button type="submit" class="btn btn-success">Publicar Proveedor</button>
			</div>
		</div>
		<div class="clear"></div>
      </form>
      </div>
    </div>
  </div>
</div>