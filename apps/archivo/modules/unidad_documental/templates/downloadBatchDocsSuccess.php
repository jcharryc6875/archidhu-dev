<?php
$path_theme = sfConfig::get('theme_simad');
$base_path = sfConfig::get('base_simad');

$currentUser = $sf_user->getAttribute('usuario_id', '', 'subscriber');
use_helper('jQuery');
?>
<script src="<?php echo $path_theme; ?>assets/js/toastr.js"></script>
<script src="<?php echo $path_theme; ?>assets/js/descarga_masiva_expedientes.js"></script>
<div class="row" id="descarga-masiva" data-url-expediente="<?php echo url_for('unidad_documental/asyncDownloadDocs'); ?>" data-url-finish="<?php echo url_for('unidad_documental/asyncDownloadDocsFinish'); ?>" data-lote="<?php echo $lote; ?>">
    <div class="col-md-12">
        <div class="progress">
            <div class="progress-bar progress-bar-striped active" role="progressbar" style="width: 0%">0 / 0</div>
        </div>
        <div class="alert alert-warning descarga-aviso" role="alert">
            <span class="glyphicon glyphicon-warning-sign"></span>
            <strong>Por favor no cierre ni recargue esta ventana</strong> hasta que el proceso termine. Al finalizar aparecer&aacute; aqu&iacute; el bot&oacute;n para descargar el archivo.
        </div>
        <!-- Contenedor Pagina -->
        <div class="panel panel-primary" data-collapsed="0">
            <div class="panel-heading">
                <div class="panel-title">
                    Lista de Expediente
                </div>
                <div class="panel-options">
                    <a href="#" target="_blank" class="btn btn-warning btn-sm pull-right descarga-link hidden"><span class="glyphicon glyphicon-download-alt"></span> Descargar ZIP</a>
                </div>
            </div>
            <!-- Contenedor Contenido Formulario-->
            <div class="panel-body with-table">
                <table class="table table-bordered table-hover table-striped responsive">
                    <thead>
                        <tr>
                            <th class="text-center" style="width: 10%">ID Expediente</th>
                            <th class="text-center" style="width: 15%">N&uacute;mero Expediente</th>
                            <th class="text-center">Nombre Expediente</th>
                            <th class="text-center" style="width: 10%">Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($list_expedientes as $unidad_documental) { ?>
                            <tr data-id="<?php echo $unidad_documental->getPrimaryKey(); ?>">
                                <td class="text-center"><?php echo $unidad_documental->getPrimaryKey(); ?></td>
                                <td class="text-center"><?php echo $unidad_documental->getCodigoBarras(); ?></td>
                                <td><?php echo $unidad_documental->getTitulo(); ?></td>
                                <td class="descarga-estado text-center">Pendiente</td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>