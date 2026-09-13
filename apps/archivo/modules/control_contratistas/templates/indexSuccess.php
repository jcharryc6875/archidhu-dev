<?php
$path_theme = sfConfig::get('theme_simad');
$base_path = sfConfig::get('base_simad');

$currentUser = $sf_user->getAttribute('usuario_id', '', 'subscriber'); 
$currentFormEditar = $sf_user->checkPerm("control_contratistas/edit", $currentUser) ;
$currentFormDelete = $sf_user->checkPerm("control_contratistas/delete", $currentUser) ;

use_helper('jQuery','Object');
?>
<div class="row">
	<div class="col-md-12">
    <?php include_once("_navbar_contratistas.php"); ?>
    <!-- Contenedor Pagina -->
	<div class="panel panel-primary" data-collapsed="0">
		<div class="panel-heading">
			<div class="panel-title">
			   Lista de Contrat&iacute;stas
			</div>
		</div>
      	<!-- Contenedor Contenido Formulario-->
		<div class="panel-body with-table">
    		<?php
    	    $cantidad_registros = $pager->getNbResults();
    	    if($cantidad_registros == 0):
    	    ?>
    			<div class="alert alert-default"><strong>No existen Registros</strong>, Intente con diferentes filtros de consulta.</div>
    	    <?php
    	    else:
    	    ?>
        		<!-- Listado contratistas -->
        		<table class="table table-bordered table-hover table-striped responsive">
        			<thead>
        				<tr>
                            <th>Consecutivo</th>
        					<th>Nombres</th>
                            <th>Apellidos</th>
        					<th>Numero identificaci&oacute;n</th>
        					<th>Cargo</th>
        					<th>Eps</th>
        					<th>Fondo pensiones</th>
        					<th>Fecha creacion</th>
                            <th>Estado</th>
        					<th>Opciones</th>
        				</tr>
        			</thead>
                    <tbody>
                    <?php foreach ($pager->getResults() as $control_contratistas): ?>
                        <tr>
                            <td class="text-center"> <?php echo jq_link_to_function(sprintf("%05d",$control_contratistas->getPrimaryKey()), 'javascript:jQuery.OpenModalSIMAD("'.$base_path.'/archivo.php/control_contratistas/show?controlcontratistas_id='.$control_contratistas->getPrimaryKey().'", 1024, 768)'); ?></td>
                            <td><?php echo $control_contratistas->getNombre() ?></td>
                            <td><?php echo $control_contratistas->getPrimerApellido(). " " .$control_contratistas->getSegundoApellido() ?></td>
                            <td><?php echo $control_contratistas->getNumeroIdentificacion() ?></td>
                            <td><?php echo $control_contratistas->getCargo() ?></td>            
                            <td><?php echo $control_contratistas->getEps() ?></td>
                            <td><?php echo $control_contratistas->getFondoPensiones() ?></td>
                            <td><?php echo $control_contratistas->getFechaCreacion() ?></td>
                            <td><?php echo $control_contratistas->getEstadoContratista() ?></td>
                            <td class="text-center">
                                <?php 
                                if($currentFormEditar){ ?>
                                    <a href="<?php echo url_for('control_contratistas/edit?controlcontratistas_id='.$control_contratistas->getPrimaryKey()) ?>" data-original-title="Editar este contratista" class = "tooltip-primary" data-toggle="tooltip">
                                    <?php echo image_tag('simad/ico_editar.png', array('border'=>"0",'width'=>"25",'align'=>"middle",)); ?></a>
                                <?php } ?>
                                <?php if($currentFormDelete){ ?>
                                    <?php echo link_to(image_tag('simad/ico_delete.png', array('border'=>"0",'width'=>"25",'align'=>"middle")), 
                                    'control_contratistas/delete?controlcontratistas_id='.$control_contratistas->getPrimaryKey(), array('post=true&confirm=Esta seguro de eliminar el registro seleccionado?','data-original-title'=>'Eliminar este contratista', 'class' => 'tooltip-primary', 'data-toggle' => 'tooltip')) ?>                
                                <?php } ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
                
                <!-- Opciones Listar -->
                <div class="row">
             	  <div class="col-sm-12 form-group">                            		
            		<?php
        		    echo link_to (image_tag('simad/ico_consultar_small.png', array('border'=>"0",'width'=>"25",'align'=>"middle")).'Consultar', 'control_contratistas/consultar', 
                        array('class' => 'btn btn-sm btn-white tooltip-primary','data-original-title'=>'Consultar contratistas', 'data-toggle' => 'tooltip'));
            		?>
                        
                    <?php
            			echo link_to (image_tag('simad/ico_exportar.png', array('border'=>"0",'width'=>"25",'align'=>"middle")).'Exportar', 'control_contratistas/excel?'.$filtros_consulta, 
                            array('class' => 'btn btn-sm btn-white tooltip-primary', 'data-original-title'=>'Generar reporte de contratistas', 'data-toggle' => 'tooltip'));
            	 	?>
                        
            		<?php
            			echo link_to (image_tag('simad/ico_crear_nuevo.png', array('border'=>"0",'width'=>"25",'align'=>"middle")).'Crear Nuevo', 'control_contratistas/create', 
                            array('class' => 'btn btn-sm btn-white tooltip-primary', 'data-original-title'=>'Crear un nuevo contratista', 'data-toggle' => 'tooltip'));
            	 	?>
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
                        echo pager_navigation($pager, 'control_contratistas/index', $filtros_consulta);
                        ?>
                      </div>
                    </div>
                  </div>
                </div>
    		<?php
    		endif;
    		?>
        </div>
    </div>
  </div>
</div>  