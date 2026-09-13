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
                Lista de Documentos del Expediente
                </div>
            </div>
            <!-- Contenedor Contenido Formulario-->
            <div class="panel-body with-table">
                <table class="table table-bordered table-hover table-striped responsive">
                    <thead>
                        <tr>
                            <th class="text-center" style="width: 10%">ID Expediente</th>
                            <th class="text-center" style="width: 10%">Id Documento</th>
                            <th class="text-center">Nombre Expediente</th>
                            <th class="text-center">Descripci&oacute;n Documento</th>
                            <th class="text-center" style="width: 10%">...</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($object = $list_docs->fetch()){ ?>
                            <tr>
                                <td class="text-center"><?php echo $object[1]; ?></td>
                                <td class="text-center"><?php echo $object[0]; ?></td>
                                <td><?php echo $object[2]; ?></td>
                                <td><?php echo $object[6]; ?></td>
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
    jQuery(window).on('load',function(){
        jQuery.ajax({
            type:'POST',
            url: '<?php echo url_for('contenido_documental/asyncDownloadDocs'); ?>',
            data: jQuery.param({ unidaddocumental_id: "<?php echo $unidaddocumental_id; ?>"}) ,
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
            complete: function(){
                javascript:jQuery.CloseLoadingStructData();
                //toastr.info('Por favor actualice esta pagina para ver los cambios'); 
            },
        });
    });
</script>