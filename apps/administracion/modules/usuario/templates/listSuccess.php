<?php
$path_theme = sfConfig::get('theme_simad');
$base_path = sfConfig::get('base_simad');
use_helper('jQuery');

?>
<div class="row">
	<div class="col-md-12">
        <?php
		// Include NavbarArchivo
		include_once("_navbar_usuario.php");
		?>
        
        <?php
	    $cantidad_registros = $pager->getNbResults();
	    if($cantidad_registros == 0):
	    ?>
        <div class="panel panel-primary">
		      <div class="panel-heading">
		        <div class="panel-title">Lista De Usuarios</div>
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
	        <div class="panel-title">Lista De Usuarios</div>
	      </div>
	      <div class="panel-body with-table">              
              <table class="table table-bordered table-hover table-striped responsive">
                  <thead>
                      <tr>                          
                          <th class="text-center">Foto</th>
                          <th class="text-center">Nombre</th>
                          <th class="text-center">Nombre Usuario</th>
                          <th class="text-center">Unidad Administrativa</th>
                          <th class="text-center">Ubicaci&oacute;n</th>
                          <th class="text-center">Estatus usuario</th>
                      </tr>
                  </thead>
                  <tbody>
                  <?php                   
                  $con = 1;$x = 0;$temp = 0;
                  foreach ($pager->getResults() as $usuario): ?>
                  <tr>
                      <td class="text-center">
                        <a href="#" onclick="javascript:jQuery.OpenModalSIMAD('<?php echo $base_path; ?>/administracion.php/usuario/show?usuario_id=<?php echo $usuario->getUsuarioId()?>');">
                          <?php 
                            if($usuario->getRutaFoto()!='' || $usuario->getNombre()!='' || $usuario->getApellido()!='' ){
                          ?> 
                            <img style="border-radius: 8px;" alt="<?php echo $usuario->getNombre()." ".$usuario->getApellido()  ?>" width="60" height="60" src="<?php echo $usuario->getRutaFoto() ?>" />
                         <?php 
                         }
                         else{ 
                  	         echo "SIN NOMBRE";
                         }
                         ?>
                        </a>
                      </td>  
                      <td><?php echo $usuario->getNombre(). " ".$usuario->getApellido(); ?></td>                      
                      <td><?php echo $usuario->getUserName()."&nbsp;" ?></td>
	                  <td><?php echo ($usuario->getDependencia())?></td>
                      <td class="text-center"><?php echo $usuario->getRegional()->getDescripcion() ?></td>
                      <td class="text-center"><?php echo $usuario->getEstadousuario()->getDescripcion() ?></td>
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
                    <a class="btn btn-white btn-sm" href="<?php echo $base_path; ?>/administracion.php/usuario/consulta"><img border="0" src="<?php echo $base_path; ?>/images/simad/ico_buscar.png" alt="Consultar Usuario" width="25" align="middle"/>Consultar Usuario</a>
                    <a class="btn btn-white btn-sm" href="<?php echo $base_path; ?>/administracion.php/usuario/create"><img border="0" src="<?php echo $base_path; ?>/images/simad/ico_crear_nuevo.png" alt="Crear Nuevo Usuario" width="25" align="middle"/>Crear Nuevo Usuario</a>
                    <a class="btn btn-white btn-sm" href="<?php echo $base_path; ?>/administracion.php/usuario/excel?<?php echo $parametros?>"><img border="0" src="<?php echo $base_path; ?>/images/simad/ico_exportar.png" alt="Exportar" width="25" align="middle"/>Exportar</a>
                </div>
            </div>
            

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
                    echo pager_navigation($pager, 'usuario/list', $parametros);
                    ?>
                  </div>
                </div>
              </div>
            </div>
            <!-- fin  paginacion -->
          </div>
        </div>                      
<?php
	endif;
?>
	</div>
</div>


