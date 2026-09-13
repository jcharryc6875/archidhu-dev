<?php 
$path_theme = sfConfig::get('theme_simad');
$base_path = sfConfig::get('base_simad');

$usuariologuiado = $sf_user->getAttribute('usuario_id','', 'subscriber');
?>

<?php
if ($total_array_msj['total_conteo'] === true) 
{
    ?>
        <div class="row">
            <div class="panel col-sm-11 panel-gradient" data-collapsed="0" style="padding-left: 0px; padding-right: 0px; margin-left: 25px;">
                <div class="panel-heading">
                    <div class="panel-title">
                        Mensajer&iacute;a
                    </div>			
                    <div class="panel-options">
                        <a href="#" data-rel="collapse"><i class="entypo-down-open"></i></a>				
                    </div>
                </div>
                
                <div class="panel-body scrollcms" data-height="250">
                    <?php
                    // Variable para verificar si todos los count son 0
                    $todosCero = true;

                    // Recorrer el array itera CADA BALDOSA.
                    foreach ($total_array_msj['data'] as $index => $elemento) 
                    {
                        if ($elemento['count'] > 0) 
                        {

                    ?>
                            <div class="col-sm-6 tooltip-primary" data-toggle="tooltip" data-original-title="Las que tienes por enviar.">
                                <?php 
                                    if($elemento['count'] > 0)     
                                    { 
                                        ?>
                                        <a href="<?php echo $base_path; ?>/servicios.php/servicio/list?porTipoServicio=<?php echo SED::encryption($elemento['tiposervicio_id']); ?>&periodo_id=<?php echo $periodo_id; ?>">
                                        <?php 
                                    } 
                                ?>
                                        <div class="tile-stats tile-plum">            
                                            <div class="icon"><i class="entypo-mail"></i></div>
                                            <div class="num" data-start="0" data-end="<?php echo $elemento['count']; ?>" data-postfix="" data-duration="1500" data-delay="0">
                                                <?php echo $elemento['count']; ?>
                                            </div>
                                            <h3>
                                                <?php echo $elemento['descripcion']; ?>
                                            </h3>
                                        </div>
                                <?php 
                                    if($elemento['count'] > 0) 
                                    { 
                                        ?>
                                        </a>
                                        <?php 
                                    } 
                                ?>
                            </div>
                    <?php
                        }
                    }
                    ?>
                </div>
        </div>
    </div>
<?php } ?>