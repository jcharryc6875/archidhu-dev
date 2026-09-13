<?php 
$path_theme = substr(sfConfig::get('theme_simad'), -1) == "/" ? sfConfig::get('theme_simad') : sfConfig::get('theme_simad').'/';
$base_path = substr(sfConfig::get('base_simad'), -1) == "/" ? sfConfig::get('base_simad') : sfConfig::get('base_simad').'/';
use_helper('Object','jQuery');

$lconfig_notificaciones = UsuarioNotificacionPeer::getAllConfigNotifyUser($usuario->getPrimaryKey());
$cantidad_registros = count($lconfig_notificaciones);
?>
<div class="row">
	<div class="col-md-12">
        <?php if($cantidad_registros == 0): ?>
            <div class="panel panel-primary">
                <div class="panel-body">
                    <div class="alert alert-default"><strong>No existen Registros</strong>, Intente con diferentes filtros de consulta.</div>
                </div>
                <!-- Opciones Listar -->
                <hr />
                <div class="row">
                    <div class="col-sm-12 form-group">
                        <a href="#" class="btn btn-white btn-sm" onclick="javascript:jQuery.OpenModalSIMAD('<?php echo $base_path; ?>administracion.php/usuario_notificacion/create?usuario_id=<?php echo $usuario->getPrimaryKey()?>','1024','600');">
                            <img border="0" src="<?php echo $base_path; ?>images/simad/ico_crear_nuevo.png" alt="Crear nueva configuracion" width="25" align="middle"/>Adicionar Nueva
                        </a>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <div class="panel panel-success">
                <div class="panel-heading">
                    <div class="panel-title">Lista de Registros</div>
                </div>
                <div class="panel-body with-table">              
                    <table class="table table-bordered table-hover table-striped responsive">
                        <thead>
                            <tr>                          
                                <th class="text-center" style="width: 25%;">Tipo Notificaci&oacute;n</th>
                                <th class="text-center" style="width: 10%;">Modulo</th>
                                <th class="text-center" style="width: 15%;">Fecha Creaci&oacute;n</th>
                                <th class="text-center" style="width: 15%;">Fecha Modificaci&oacute;n</th>
                                <th class="text-center" style="width: 15%;">...</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($lconfig_notificaciones as $item_config): ?>
                                <tr>
                                    <td class="text-center">
                                        <a href="#" onclick="javascript:jQuery.OpenModalSIMAD('<?php echo $base_path; ?>administracion.php/usuario_notificacion/edit?usuarionotificacion_id=<?php echo $item_config->getPrimaryKey()?>');">
                                            <?php 
                                                echo $item_config->getTipoNotificacion()->getDescripcion();
                                            ?>
                                        </a>
                                    </td>  
                                    <td class="text-center"><?php echo $item_config->getTipoNotificacion()->getModulo(); ?></td>                      
                                    <td class="text-center"><?php echo $item_config->getFechaCreacion() ?></td>
                                    <td class="text-center"><?php echo $item_config->getFechaModificacion() ?></td>
                                    <td class="text-center"><?php echo "&nbsp;" ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    <!-- Opciones Listar -->
                    <hr />
                    <div class="row">
                        <div class="col-sm-12 form-group">                                    
                            <a href="#" class="btn btn-white btn-sm" onclick="javascript:jQuery.OpenModalSIMAD('<?php echo $base_path; ?>administracion.php/usuario_notificacion/create?usuario_id=<?php echo $usuario->getPrimaryKey()?>','1024','600');">
                                <img border="0" src="<?php echo $base_path; ?>images/simad/ico_crear_nuevo.png" alt="Crear nueva configuracion" width="25" align="middle"/>Adicionar Nueva
                            </a>
                        </div>
                    </div>
                </div>
            </div>                      
        <?php endif; ?>
	</div>
</div>