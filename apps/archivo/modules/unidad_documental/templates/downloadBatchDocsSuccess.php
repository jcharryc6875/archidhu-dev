<?php
$path_theme = sfConfig::get('theme_simad');
$base_path = sfConfig::get('base_simad');

$currentUser = $sf_user->getAttribute('usuario_id', '', 'subscriber');
use_helper('jQuery');
?>
<script src="<?php echo $path_theme; ?>assets/js/toastr.js"></script>
<div class="row">
    <div class="col-md-12">
        <!-- Contenedor Pagina -->
        <div class="panel panel-primary" data-collapsed="0">
            <div class="panel-heading">
                <div class="panel-title">
                    Lista de Expediente
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
                            <th class="text-center" style="width: 10%">...</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($list_expedientes as $unidad_documental) { ?>
                            <tr>
                                <td class="text-center"><?php echo $unidad_documental->getPrimaryKey(); ?></td>
                                <td class="text-center"><?php echo $unidad_documental->getCodigoBarras(); ?></td>
                                <td><?php echo $unidad_documental->getTitulo(); ?></td>
                                <td><?php echo "&nbsp;"; ?></td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    jQuery(window).on('load', function() {
        jQuery.ajax({
            type: 'POST',
            url: '<?php echo url_for('unidad_documental/asyncDownloadDocs'); ?>',
            data: jQuery.param({
                localizacionunidaddocumental_id: "<?php echo $localizacionunidaddocumental_id; ?>"
            }),
            contentType: 'application/x-www-form-urlencoded; charset=UTF-8',
            cache: false,
            processData: false,
            beforeSend: function() {
                javascript: jQuery.LoadingStructData();
            },
            success: function(data) {
                javascript: jQuery.CloseLoadingStructData();
                try {
                    if (data.httpStatus == 200) {
                        toastr.success(data.message);
                        if (data.url_descarga.length > 0) {
                            window.open(data.url_descarga, '_blank');
                        }
                    } else if (data.httpStatus == 400) {
                        toastr.error(data.message);
                    } else {
                        toastr.warning(data.message);
                    }
                } catch (err) {
                    toastr.error(err.message);
                }
            },
            complete: function() {
                javascript: jQuery.CloseLoadingStructData();
                //toastr.info('Por favor actualice esta pagina para ver los cambios'); 
            },
        });
    });
</script>