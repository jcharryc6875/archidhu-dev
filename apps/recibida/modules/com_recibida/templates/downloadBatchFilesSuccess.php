<?php
$path_theme = sfConfig::get('theme_simad');
use_helper('jQuery');
?>
<script src="<?php echo $path_theme; ?>assets/js/toastr.js"></script>
<script src="<?php echo $path_theme; ?>assets/js/descarga_masiva_expedientes.js"></script>
<div class="row" id="descarga-masiva" data-url-expediente="<?php echo url_for('com_recibida/printBatchFile'); ?>" data-url-finish="<?php echo url_for('com_recibida/printBatchFileFinish'); ?>" data-lote="<?php echo $lote; ?>" data-param="ids" data-unidad="trozo(s)">
    <div class="col-md-12">
        <div class="progress">
            <div class="progress-bar progress-bar-striped active" role="progressbar" style="width: 0%">0 / 0</div>
        </div>
        <div class="alert alert-warning descarga-aviso" role="alert">
            <span class="glyphicon glyphicon-warning-sign"></span>
            <strong>Por favor no cierre ni recargue esta ventana</strong> hasta que el proceso termine. Al finalizar aparecer&aacute; aqu&iacute; el bot&oacute;n para descargar el archivo.
        </div>
        <div class="panel panel-primary" data-collapsed="0">
            <div class="panel-heading">
                <a href="#" target="_blank" class="btn btn-success btn-sm pull-right descarga-link hidden"><span class="glyphicon glyphicon-download-alt"></span> Descargar ZIP</a>
                <div class="panel-title">
                    Radicados marcados para descarga
                </div>
            </div>
            <div class="panel-body with-table">
                <table class="table table-bordered table-hover table-striped responsive">
                    <thead>
                        <tr>
                            <th class="text-center" style="width: 10%">Fragmento</th>
                            <th class="text-center">Radicados</th>
                            <th class="text-center" style="width: 25%">Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $first = 1; ?>
                        <?php foreach ($chunks as $index => $chunk) { ?>
                            <tr data-id="<?php echo implode74(',', $chunk); ?>">
                                <td class="text-center"><?php echo $index + 1; ?></td>
                                <td class="text-center"><?php echo $first; ?> al <?php echo $first + count74($chunk) - 1; ?></td>
                                <td class="descarga-estado text-center">Pendiente</td>
                            </tr>
                            <?php $first += count74($chunk); ?>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>