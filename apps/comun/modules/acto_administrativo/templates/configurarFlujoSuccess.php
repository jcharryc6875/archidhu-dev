<?php
$path_theme = sfConfig::get('theme_simad');
use_helper('Object', 'jQuery');
?>
<script src="<?php echo $path_theme; ?>assets/js/toastr.js"></script>
<script src="<?php echo $path_theme; ?>assets/js/bootstrap-switch.min.js"></script>
<script src="<?php echo $path_theme; ?>assets/js/jquery-ui/js/jquery-ui-1.10.3.custom.js"></script>

<div class="row">
    <div class="col-md-12">
        <div class="panel panel-gradient" data-collapsed="0">
            <div class="panel-heading">
                <div class="panel-title">Configurar Flujo de este Acto Administrativo</div>
            </div>
            <div class="panel-body">
                <?php include_partial('configurarFlujo', array(
                    'acto_administrativo' => $acto_administrativo,
                    'participantesFlujo' => $participantesFlujo,
                    'etapasConfigActo' => $etapasConfigActo,
                )); ?>
            </div>
        </div>
    </div>
</div>