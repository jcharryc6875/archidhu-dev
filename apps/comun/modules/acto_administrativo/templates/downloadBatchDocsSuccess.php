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
					Lista de Actos Administrativos
                </div>
            </div>
            <!-- Contenedor Contenido Formulario-->
            <div class="panel-body with-table">
                <table class="table table-bordered table-hover table-striped responsive">
                    <thead>
                        <tr>
                            <th class="text-center" style="width: 15%">N&uacute;mero Acto Administrativo</th>
                            <th class="text-center" style="width: 25%">Dependencia</th>
                            <th class="text-center" style="width: 25%">Subserie</th>
                            <th class="text-center">Asunto</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($list_registros as $row){ ?>
                            <tr>
                                <td class="text-center">
                                    <?php echo !empty($row->getNumeroResolucion()) ? $row->getRadicadoCompuesto() : "Sin Radicar"; ?>
                                </td>
                                <td><?php echo $row->getDependencia()->getNombreCustom(); ?></td>
                                <td><?php echo $row->getSubserie()->getDescripcion(); ?></td>
                                <td><?php echo $row->getAsunto(); ?></td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    jQuery(window).on('load',function(){
        jQuery.ajax({
            type:'POST',
            url: '<?php echo url_for('acto_administrativo/asyncDownloadDocs'); ?>',
            contentType: 'application/x-www-form-urlencoded; charset=UTF-8',
            cache:false,
            processData: false,
            beforeSend: function() {
                javascript:jQuery.LoadingStructData();
            },
            success:function(data){
                javascript:jQuery.CloseLoadingStructData();
                try {
                    if(data.httpStatus == 200){
                        toastr.success(data.message);
                        if (data.url_descarga.length > 0) { window.open(data.url_descarga , '_blank'); }
                    }else if(data.httpStatus == 400){ 
                        toastr.error(data.message);
                    }else{ 
                        toastr.warning(data.message); 
                    }
                }catch(err) {
                    toastr.error(err.message);
                }
            },
            error: function(){
                javascript:jQuery.CloseLoadingStructData();
                toastr.error('Error interno en el servidor');
            },
            complete: function(){
                javascript:jQuery.CloseLoadingStructData();
            },
        });
    });
</script>