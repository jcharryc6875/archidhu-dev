<?php 
$path_theme = sfConfig::get('theme_simad');
$base_path = sfConfig::get('base_simad');
use_helper('Object','jQuery');
?>
<div class="row">
	<div class="col-md-6">
    	<!-- Contenedor Pagina -->
    	<div class="panel panel-gradient" data-collapsed="0">
    		<div class="panel-heading">
    			<div class="panel-title">
    			  Cambio de password
    			</div>
    		</div>
            <?php if($mensajeError==1){ ?>
                <form name="form1" id="form1" action="<?php echo $form_actions; ?>">                            
                    <div class="row">
            			<div class="col-md-12">
            				<div class="alert alert-danger"><strong>Error!</strong> Tu password no fue actualizado, El password anterior no es correcto.</div>
            			</div>			
            		</div>
                </form>
                <script type="text/javascript">
                    alert('Error al acutualizar el password, por favor intente de nuevo?');
                    document.form1.submit();
                </script>
            <?php }else{ ?>
                <form name="form1" id="form1" action="<?php echo $base_path.'/'.$form_actions ?>">
                    <div class="row">
            			<div class="col-md-12">
            				<div class="alert alert-success"><strong>Grandioso!</strong> Tu password fue actualizado existosamente.</div>
            			</div>			
            		</div>
                    
                    <div class="row">
                        <div class="form-group">
                            <!-- Botonera -->
                            <div class="col-sm-offset-4 col-sm-5">
                                <button type="submit" class="btn btn-success">Aceptar</button>
                            </div>
                        </div>
                    </div>
                </form>
                <script type="text/javascript">
                    alert('Grandioso! Tu password fue actualizado existosamente');
                    document.form1.submit();
                </script>
            <?php } ?>
         </div>
     </div>
</div>