<?php
use_helper('Object', 'jQuery');
$path_theme = sfConfig::get('theme_simad');
$base_path = sfConfig::get('base_simad');
?>
<script src="<?php echo $path_theme; ?>assets/js/bootstrap-tabcollapse.js"></script>
<script src="<?php echo $path_theme; ?>assets/js/datatables/v1.13.7/jquery.dataTables.min.js"></script>
<script src="<?php echo $path_theme; ?>assets/js/bootstrap-switch.min.js"></script>
<link src="<?php echo $path_theme; ?>assets/js/datatables/v1.13.7/jquery.dataTables.min.css"></script>

<div class="row">
    <div class="col-md-12">
        <div class="panel panel-primary">
            <div class="panel-heading">
                <div class="panel-title">Lista de Plantillas Comunicaciones</div>
            </div>
            <div class="panel-body with-table">
                <table class="table table-bordered table-hover table-striped responsive displayinfo datatable">
                    <thead>
                        <tr class="replace-inputs">
                            <th>C&oacute;digo</th>
                            <th>Dependencia</th>
                            <th>Regional</th>
                            <th>Nombre</th>
                            <th>Descripci&oacute;n</th>
                            <th>Modulo</th>
                            <th>Opciones</th>
                        </tr>
                        <tr>
                            <th class="text-center" style="width: 8%;">C&oacute;digo</th>
                            <th class="text-center" style="width: 10%;">Dependencia</th>
                            <th class="text-center" style="width: 10%;">Regional</th>
                            <th class="text-center" style="width: 15%;">Nombre</th>
                            <th class="text-center" style="width: 25%;">Descripci&oacute;n</th>
                            <th class="text-center" style="width: 5%;">Modulo</th>
                            <th class="text-center" style="width: 5%;">Firma Desatendida</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($plantillas_com as $object) : ?>
                            <tr>
                                <td class="text-center"><?php echo $object->getCodigo(); ?></td>
                                <td><?php echo $object->getDependencia(); ?></td>
                                <td><?php echo $object->getRegional(); ?></td>
                                <td><?php echo $object->getNombre(); ?></td>
                                <td><?php echo $object->getDescripcion(); ?></td>
                                <td class="text-center"><?php echo $object->getModulo(); ?></td>
                                <td class="text-center">
                                    <div class="bs-tplcom-options">
										<div class="make-switch is-radio switch-small tplcom-options" data-on="success" data-off="danger" data-on-label="<i class='entypo-check'></i>" data-off-label="<i class='entypo-cancel'></i>">
                                            <input type="checkbox" data-pkobject="<?php echo SED::encryption($object->getPrimaryKey()) ?>" <?php echo $object->getFirmaDesatendida() ? "checked" : ""?> />
                                        </div>
									</div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    var responsiveHelper;
    var breakpointDefinition = {
        tablet: 1024,
        phone : 480
    };

    var tableContainer;

    jQuery(document).ready(function($){
        tableContainer = jQuery("table.displayinfo");
        
        tableContainer.dataTable({
            /*"responsive": true,*/
            "sPaginationType": "bootstrap",
            "aLengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
            "bLengthChange": true,
            "bFilter": true, 
            "bInfo": true,
            "bStateSave": true,
            "language": {
                "lengthMenu": "Mostrando _MENU_ registros por pagina",
                "zeroRecords": "Lo sentimos, Ningun registro encontrado",
                /*"info": "Mostrado Pagina _PAGE_ de _PAGES_",*/
                "info": "Mostrado _START_ a _END_ de _TOTAL_ registros",
                "infoEmpty": "Ningun registro encontrado",
                "search": "Buscar:",
                "infoFiltered": "(Registros filtrados de un total de _MAX_ registros)"
            }
        });

        tableContainer.columnFilter({
        "sPlaceHolder" : "head:after"
        });

        jQuery(".dataTables_wrapper select").select2({
            minimumResultsForSearch: -1
        });

        jQuery('[data-toggle="tooltip"]').tooltip();
    });
</script>