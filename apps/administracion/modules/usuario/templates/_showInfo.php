<?php 
$path_theme = sfConfig::get('theme_simad');
$base_path = sfConfig::get('base_simad');
use_helper('Object','jQuery');
?>
<?php if ($sf_user->hasFlash('messages_info')): ?>
    <div class="alert alert-success"><strong>Excelente! </strong><?php echo $sf_user->getFlash('messages_info') ?></div>
<?php endif ?>
<?php if ($sf_user->hasFlash('messages_error')): ?>
    <div class="alert alert-danger"><strong>Opps! </strong><?php echo $sf_user->getFlash('messages_error') ?></div>
<?php endif ?>
<!-- Informacion Detalle -->
<div class="col-sm-12 col-md-12">
    <div class="row">
        <div class="col-sm-6">
            <div class="col-sm-4"><p><strong>Estado: </strong></p></div>
            <div class="col-sm-8"><p><?php echo $usuario->getEstadousuario()->getDescripcion() ?></p></div>
        </div>
        <div class="col-sm-6">
            <div class="col-sm-5"><p><strong>Unidad Administrativa: </strong></p></div>
            <div class="col-sm-7"><?php echo $usuario->getDependencia() ?></div>
        </div>
    </div>
    
    <div class="row">
        <div class="col-sm-6">
            <div class="col-sm-4"><p><strong>Regional: </strong></p></div>
            <div class="col-sm-8"><p><?php echo $usuario->getRegional()->getDescripcion() ?></p></div>
        </div>
        <div class="col-sm-6">
            <div class="col-sm-5"><p><strong>User Name:</strong></p></div>
            <div class="col-sm-7"><p><?php echo $usuario->getUserName() ?></p></div>
        </div>
    </div>
    
    <div class="row">
        <div class="col-sm-6">
            <div class="col-sm-4"><p><strong>Password: </strong></p></div>
            <div class="col-sm-8"><p><?php echo $usuario->getPassword() ?></p></div>
        </div>
        <div class="col-sm-6">
            <div class="col-sm-5"><p><strong>Fecha de Actualizaci&oacute;n:</strong></p></div>
            <div class="col-sm-7"><p><?php echo $usuario->getFechaActualizacion();  ?></p></div>
        </div>
    </div>
    
    <div class="row">
        <div class="col-sm-6">
            <div class="col-sm-4"><p><strong>Nombre:</strong></p></div>
            <div class="col-sm-8"><p><?php echo $usuario->getNombre()." ".$usuario->getApellido() ?></p></div>
        </div>
        <div class="col-sm-6">
            <div class="col-sm-5"><p><strong>Cedula:</strong></p></div>
            <div class="col-sm-7"><p><?php echo $usuario->getCedula() ?></p></div>
        </div>
    </div>
    
    <div class="row">
        <div class="col-sm-6">
            <div class="col-sm-4"><p><strong>Email:</strong></p></div>
            <div class="col-sm-8"><p><?php echo $usuario->getEmail() ?></p></div>
        </div>
        <div class="col-sm-6">
            <div class="col-sm-5"><p><strong>Iniciales:</strong></p></div>
            <div class="col-sm-7"><p><?php echo $usuario->getIniciales() ?></p></div>
        </div>
    </div>
    
    <div class="row">
        <div class="col-sm-6">
            <div class="col-sm-4"><p><strong>Tipo Usuario:</strong></p></div>
            <div class="col-sm-8"><p><?php echo $usuario->getTipousuarioId() ? $usuario->getTipoUsuario() : "N/A"; ?></p></div>
        </div>
        <div class="col-sm-6">
            <div class="col-sm-5"><p><strong>Tipo Autenticaci&oacute;n:</strong></p></div>
            <div class="col-sm-7"><p><?php echo $usuario->getTipoautenticacionId() ? $usuario->getTipoAutenticacion() : "N/A"; ?></p></div>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-6">
            <div class="col-sm-4"><p><strong>Extensi&oacute;n:</strong></p></div>
            <div class="col-sm-8"><p><?php echo $usuario->getExtension() ?></p></div>
        </div>
        <div class="col-sm-6">
            <div class="col-sm-5"><p><strong>Intentos:</strong></p></div>
            <div class="col-sm-7"><p><?php echo $usuario->getIntentos() ?></p></div>
        </div>
    </div>
    
    <div class="row">
        <div class="col-sm-6">
            <div class="col-sm-4"><p><strong>Prefijo:</strong></p></div>
            <div class="col-sm-8"><p><?php echo $usuario->getPrefijo() ?></p></div>
        </div>
        <div class="col-sm-6">
            <div class="col-sm-5"><p><strong>Fecha Creaci&oacute;n:</strong></p></div>
            <div class="col-sm-7"><p><?php echo $usuario->getFechaCreacion() ?></p></div>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-6">
            <div class="col-sm-4"><p><strong>Proveedor Firma Digital:</strong></p></div>
            <div class="col-sm-8"><p><?php echo $usuario->getProveedorFirmaDigital() ?></p></div>
        </div>
        <div class="col-sm-6">
            <div class="col-sm-5"><p><strong>&nbsp;</strong></p></div>
            <div class="col-sm-7"><p><?php echo "&nbsp;" ?></p></div>
        </div>
    </div>
    
    <div class="row">
        <div class="col-sm-6">
            <div class="col-sm-4"><p><strong>Cargos:</strong></p></div>
            <div class="col-sm-8"><p>
            <?php 
                foreach($objCargoUsuario as $objCargoUsuarios)
                {
                    echo $objCargoUsuarios->getCargo()->getDescripcion()." , "; 
                }  
            ?>
            </p></div>
        </div>
        <div class="col-sm-6">
            <div class="col-sm-5"><p><strong>Usuario Active Directory:</strong></p></div>
            <div class="col-sm-7"><p><?php echo $usuario->getUsuarioAd() ?></p></div>
        </div>
    </div>
    
    <?php $rolCom = UsuarioPeer::getUsuarioPocesoComListText($usuario->getPrimaryKey()); ?>
    <?php if(count($rolCom)){ ?>
        <div class="row">
            <div class="col-sm-6">
                <div class="col-sm-4"><p><strong>Rol Comunicaciones:</strong></p></div>
                <div class="col-sm-8">
                    <p>
                        <?php echo implode(",",$rolCom); ?>
                    </p>
                </div>
            </div>						
        </div>
    <?php } ?>

    <?php $perfiles_usuario = UsuarioPeer::getPerfilesByUsuarioId($usuario->getPrimaryKey()); ?>
    <?php if(count($perfiles_usuario)){ ?>
        <div class="row">
            <div class="col-sm-6">
                <div class="col-sm-4"><p><strong>Perfiles:</strong></p></div>
                <div class="col-sm-8">
                    <p>
                        <?php echo implode(",",$perfiles_usuario); ?>
                    </p>
                </div>
            </div>						
        </div>
    <?php } ?>
    
    <div class="row">
        <div class="col-sm-6">
            <div class="col-sm-4"><p><strong>Firma Digital Habilitada:</strong></p></div>
            <div class="col-sm-8"><p><?php echo $usuario->getFirmaDigital() ? "Si" : "No"; ?></p></div>
        </div>
        <div class="col-sm-6">
            <div class="col-sm-5"><p><strong>Firma Desatendida Habilitada:</strong></p></div>
            <div class="col-sm-7"><p><?php echo $usuario->getFirmaDesatendida() ? "Si" : "No"; ?></p></div>
        </div>
    </div>
    
    <div class="row">
        <div class="col-sm-6">
            <div class="col-sm-4"><p><strong>Activar Alertas:</strong></p></div>
            <div class="col-sm-8"><p><?php echo $usuario->getActivarAlertas() ? "Si" : "No"; ?></p></div>
        </div>
        <div class="col-sm-6">
            <div class="col-sm-5"><p><strong>Fecha Expriraci&oacute;n:</strong></p></div>
            <div class="col-sm-7"><p><?php echo $usuario->getFechaExpiracion("Y-m-d"); ?></p></div>
        </div>
    </div>
    
    <div class="row">
        <div class="col-sm-6">
            <div class="col-sm-4"><p><strong>Jefe Area:</strong></p></div>
            <div class="col-sm-8"><p><?php echo $usuario->getReceptorDep() ? "Si" : "No"; ?></p></div>
        </div>
        <div class="col-sm-6">
            <div class="col-sm-5"><p><strong>&nbsp;</strong></p></div>
            <div class="col-sm-7"><p><?php echo "&nbsp;"; ?></p></div>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-6">
            <div class="col-sm-4"><p><strong>Ruta Foto:</strong></p></div>
            <div class="col-sm-8"><p><img style="border-radius: 8px;" alt="<?php echo $usuario->getNombre()." ".$usuario->getApellido()  ?>" width="180px" height="210px" src="<?php echo $usuario->getRutaFoto() ?>" /></p></div>
        </div>
        <?php if(trim($usuario->getFirmaElectronica())){ ?>
            <div class="col-sm-6">
                <div class="col-sm-5"><p><strong>Firma Electr&oacute;nica:</strong></p></div>
                <div class="col-sm-7"><p><img width="150px" height="80px" src="<?php echo $usuario->getFirmaElectronica() ?>" /></p></div>
            </div>
        <?php } ?>
    </div>
</div>