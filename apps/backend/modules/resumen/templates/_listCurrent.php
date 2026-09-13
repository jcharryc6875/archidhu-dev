<?php 
$path_theme = sfConfig::get('theme_simad');
$base_path = sfConfig::get('base_simad');

$dias = array("Domingo","Lunes","Martes","Miercoles","Jueves","Viernes","S&acute;bado");
$meses = array("Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre");

$usuariologuiado = $sf_user->getAttribute('usuario_id','', 'subscriber');
?>

<!-- COMUNICACIONES RECIBIDAS --> 
<div class="row">
    <div class="panel col-sm-11 panel-gradient" data-collapsed="0" style="padding-left: 0px; padding-right: 0px; margin-left: 25px;">
        <div class="panel-heading">
            <div class="panel-title">
                    Comunicaciones Externas Recibidas
            </div>			
            <div class="panel-options">
                <a href="#" data-rel="collapse"><i class="entypo-down-open"></i></a>				
            </div>
        </div>
        
        <div class="panel-body scrollcms" data-height="250">
            <div class="col-sm-6 tooltip-primary" data-toggle="tooltip" data-original-title="Las que tienes por leer.">
                <?php if($recibidas_leer) { ?>
                    <a href="<?php echo $base_path; ?>/recibida.php/com_recibida/list?leer=1&periodo_id=<?php echo $periodo_id; ?>">
                <?php } ?>
                        <div class="tile-stats tile-plum">            
                            <div class="icon"><i class="entypo-mail"></i></div>
                            <div class="num" data-start="0" data-end="<?php echo $recibidas_leer ?>" data-postfix="" data-duration="1500" data-delay="0"><?php echo $recibidas_leer ?></div>
                            <h3>Por Leer</h3>
                        </div>
                <?php if($recibidas_leer) { ?>
                    </a>
                <?php } ?>
            </div>
            
            <div class="col-sm-6 tooltip-primary" data-toggle="tooltip" data-original-title="Las que te enviaron copia.">
                <?php if($recibida_copia) { ?>
                    <a href="<?php echo $base_path; ?>/recibida.php/com_recibida/list?usuario_copia=<?php echo $usuariologuiado."&periodo_id=".$periodo_id; ?>">
                <?php } ?>
                        <div class="tile-stats tile-blue">            
                            <div class="icon"><i class="entypo-shuffle"></i></div>
                            <div class="num" data-start="0" data-end="<?php echo $recibida_copia ?>" data-postfix="" data-duration="1500" data-delay="0"><?php echo $recibida_copia ?></div>
                            <h3>Copias</h3>
                        </div>
                <?php if($recibida_copia) { ?>
                    </a>
                <?php } ?>
            </div>
            
            <div class="col-sm-6 tooltip-primary" data-toggle="tooltip" data-original-title="Las que tienes vencidas.">
                <?php if($vencidas) { ?>
                    <a href="<?php echo $base_path; ?>/recibida.php/com_recibida/list?vencidas=1">
                <?php } ?>
                        <div class="tile-stats tile-red">            
                            <div class="icon"><i class="entypo-mail"></i></div>
                            <div class="num" data-start="0" data-end="<?php echo $vencidas ?>" data-postfix="" data-duration="1500" data-delay="0"><?php echo $vencidas ?></div>
                            <h3>Vencidas</h3>
                        </div>
                <?php if($vencidas) { ?>
                    </a>
                <?php } ?>
            </div>
            
            <div class="col-sm-6 tooltip-primary" data-toggle="tooltip" data-original-title="Las que estan proximas a vencer.">
                <?php if($por_vencer) { ?>
                    <a href="<?php echo $base_path; ?>/recibida.php/com_recibida/list?porVencer=1">
                <?php } ?>
                        <div class="tile-stats tile-pink">            
                            <div class="icon"><i class="entypo-shuffle"></i></div>
                            <div class="num" data-start="0" data-end="<?php echo $por_vencer ?>" data-postfix="" data-duration="1500" data-delay="0"><?php echo $por_vencer ?></div>
                            <h3>Por Vencer</h3>                            
                        </div>
                <?php if($por_vencer) { ?>
                    </a>
                <?php } ?>
            </div>                        
            
            <?php if(!empty($por_distribuir)) { ?>
                <div class="col-sm-6 tooltip-primary" data-toggle="tooltip" data-original-title="Las que tienes por distribuir.">
                    <?php if($por_distribuir) { ?>
                        <a href="<?php echo $base_path; ?>/recibida.php/com_recibida/list?porProcesoCom=<?php echo md5(2)."&periodo_id=".$periodo_id; ?>">
                    <?php } ?>
                    <div class="tile-stats tile-red">            
                        <div class="icon"><i class="entypo-mail"></i></div>
                        <div class="num" data-start="0" data-end="<?php echo $por_distribuir ?>" data-postfix="" data-duration="1500" data-delay="0"><?php echo $por_distribuir ?></div>
                        <h3>Por Distribuir</h3>
                    </div>
                    <?php if($por_distribuir) { ?>
                        </a>
                    <?php } ?>
                </div>
            <?php } ?>
            
            <?php if(!empty($por_gestionar)) { ?>
                <div class="col-sm-6 tooltip-primary" data-toggle="tooltip" data-original-title="Las que tienes por gestionar.">
                    <?php if($por_gestionar) { ?>
                        <a href="<?php echo $base_path; ?>/recibida.php/com_recibida/list?porProcesoCom=<?php echo md5(3)."&periodo_id=".$periodo_id; ?>">
                    <?php } ?>
                    <div class="tile-stats tile-blue">            
                        <div class="icon"><i class="entypo-mail"></i></div>
                        <div class="num" data-start="0" data-end="<?php echo $por_gestionar ?>" data-postfix="" data-duration="1500" data-delay="0"><?php echo $por_gestionar ?></div>
                        <h3>Por Gestionar</h3>
                    </div>
                    <?php if($por_gestionar) { ?>
                        </a>
                    <?php } ?>
                </div>
            <?php } ?>
            
            <?php if(!empty($por_ccalidad)) { ?>
                <div class="col-sm-6 tooltip-primary" data-toggle="tooltip" data-original-title="Las que tienes en control de calidad.">
                    <?php if($por_ccalidad) { ?>
                        <a href="<?php echo $base_path; ?>/recibida.php/com_recibida/list?porProcesoCom=<?php echo md5(6)."&periodo_id=".$periodo_id; ?>">
                    <?php } ?>
                    <div class="tile-stats tile-cyan">            
                        <div class="icon"><i class="entypo-mail"></i></div>
                        <div class="num" data-start="0" data-end="<?php echo $por_ccalidad ?>" data-postfix="" data-duration="1500" data-delay="0"><?php echo $por_ccalidad ?></div>
                        <h3>Control Calidad</h3>
                    </div>
                    <?php if($por_ccalidad) { ?>
                        </a>
                    <?php } ?>
                </div>
            <?php } ?>

            <?php if(1 != 1){ ?>
                <div class="col-sm-6 tooltip-primary" data-toggle="tooltip" data-original-title="Workflows que te han asignado.">
                    <?php if($workflow_recibida) { ?>
                        <a href="<?php echo $base_path; ?>/recibida.php/com_recibida/list?porWorkflow=1&periodo_id=<?php echo $periodo_id; ?>">
                    <?php } ?>
                            <div class="tile-stats tile-cyan">            
                                <div class="icon"><i class="entypo-shuffle"></i></div>
                                <div class="num" data-start="0" data-end="<?php echo $workflow_recibida ?>" data-postfix="" data-duration="1500" data-delay="0"><?php echo $workflow_recibida ?></div>
                                <h3>Workflows</h3>
                            </div>
                    <?php if($workflow_recibida) { ?>
                        </a>
                    <?php } ?>
                </div>
                
                <div class="col-sm-6 tooltip-primary" data-toggle="tooltip" data-original-title="Facturas que tienes pendientes por revisar y aprobar.">
                    <?php if($facturas_recibida) { ?>
                        <a href="<?php echo $base_path; ?>/recibida.php/factura/list?bystatus=<?php echo md5($usuariologuiado)."&periodo_id=".$periodo_id; ?>">
                    <?php } ?>
                            <div class="tile-stats tile-orange">            
                                <div class="icon"><i class="entypo-shuffle"></i></div>
                                <div class="num" data-start="0" data-end="<?php echo $facturas_recibida ?>" data-postfix="" data-duration="1500" data-delay="0"><?php echo $facturas_recibida ?></div>
                                <h3>Facturas</h3>
                            </div>
                    <?php if($facturas_recibida) { ?>
                        </a>
                    <?php } ?>
                </div>
            <?php } ?>
        </div>
    </div>
</div>

<!-- COMUNICACIONES INTERNAS -->
<div class="row">
    <div class="panel col-sm-11 panel-gradient" data-collapsed="0" style="padding-left: 0px; padding-right: 0px; margin-left: 25px;">
        <div class="panel-heading">
            <div class="panel-title">
                    Comunicaciones Internas
            </div>			
            <div class="panel-options">
                <a href="#" data-rel="collapse"><i class="entypo-down-open"></i></a>				
            </div>
        </div>
        
        <div class="panel-body scrollcms" data-height="250" > 
            <div class="col-sm-6 tooltip-primary" data-toggle="tooltip" data-original-title="Las que tienes por leer.">
                <?php if($por_leer) { ?>
                    <a href="<?php echo $base_path; ?>/interna.php/com_interna/list?estadocominterna_id=2&porFunciEntrada=1&periodo_id=<?php echo $periodo_id; ?>">
                <?php } ?>
                        <div class="tile-stats tile-plum">            
                            <div class="icon"><i class="entypo-mail"></i></div>
                            <div class="num" data-start="0" data-end="<?php echo $por_leer ?>" data-postfix="" data-duration="1500" data-delay="0"><?php echo $por_leer ?></div>
                            <h3>Por Leer</h3>
                        </div>
                <?php if($por_leer) { ?>
                    </a>
                <?php } ?>
            </div>
            
            <div class="col-sm-6 tooltip-primary" data-toggle="tooltip" data-original-title="Las que te enviaron copia.">
                <?php if($copia) { ?>
                    <a href="<?php echo $base_path; ?>/interna.php/com_interna/list?usuarioCopia=<?php echo $usuariologuiado."&periodo_id=".$periodo_id; ?>">
                <?php } ?>
                        <div class="tile-stats tile-blue">            
                            <div class="icon"><i class="entypo-mail"></i></div>
                            <div class="num" data-start="0" data-end="<?php echo $copia ?>" data-postfix="" data-duration="1500" data-delay="0"><?php echo $copia ?></div>
                            <h3>Copias</h3>
                        </div>
                <?php if($copia) { ?>
                    </a>
                <?php } ?>
            </div>
            
            <div class="col-sm-6 tooltip-primary" data-toggle="tooltip" data-original-title="Las que tienes pendientes por responder">
                <?php if($por_responder) { ?>
                    <a href="<?php echo $base_path; ?>/interna.php/com_interna/list?estadocominterna_id=5&porFunciEntrada=1&periodo_id=<?php echo $periodo_id; ?>">
                <?php } ?>
                        <div class="tile-stats tile-orange">            
                            <div class="icon"><i class="entypo-mail"></i></div>
                            <div class="num" data-start="0" data-end="<?php echo $por_responder ?>" data-postfix="" data-duration="1500" data-delay="0"><?php echo $por_responder ?></div>
                            <h3>Por Responder</h3>
                        </div>
                <?php if($por_responder) { ?>
                    </a>
                <?php } ?>
            </div>
            
            <?php if(!empty($internas_revisor)) { ?>
                <div class="col-sm-6 tooltip-primary" data-toggle="tooltip" data-original-title="Las que tienes pendientes por revisar y aprobar">
                    <?php if($internas_revisor) { ?>
                        <a href="<?php echo $base_path; ?>/interna.php/com_interna/list?porProcesoCom=<?php echo md5(4)."&periodo_id=".$periodo_id; ?>">
                    <?php } ?>
                        <div class="tile-stats tile-black">            
                            <div class="icon"><i class="entypo-mail"></i></div>
                            <div class="num" data-start="0" data-end="<?php echo $internas_revisor ?>" data-postfix="" data-duration="1500" data-delay="0"><?php echo $internas_revisor ?></div>
                            <h3>Por Revisar</h3>
                        </div>
                    <?php if($internas_revisor) { ?>
                        </a>
                    <?php } ?>
                </div>
            <?php } ?>
            
            <?php if(!empty($internas_firmas)) { ?>
                <div class="col-sm-6 tooltip-primary" data-toggle="tooltip" data-original-title="Las que tienes pendientes por aprobar y firmar">
                    <?php if($internas_firmas) { ?>
                        <a href="<?php echo $base_path; ?>/interna.php/com_interna/list?porProcesoCom=<?php echo md5(2)."&periodo_id=".$periodo_id; ?>">
                    <?php } ?>
                        <div class="tile-stats tile-cyan">            
                        <div class="icon"><i class="entypo-mail"></i></div>
                            <div class="num" data-start="0" data-end="<?php echo $internas_firmas ?>" data-postfix="" data-duration="1500" data-delay="0"><?php echo $internas_firmas ?></div>
                        <h3>Por Firmar</h3>
                    </div>
                    <?php if($internas_firmas) { ?>
                        </a>
                    <?php } ?>
                </div>
            <?php } ?>

            <?php if(!empty($internas_prufirmas)) { ?>
                <div class="col-sm-6 tooltip-primary" data-toggle="tooltip" data-original-title="Las que tienes pendientes por aprobar y firmar urgentes">
                    <?php if($internas_prufirmas) { ?>
                        <a href="<?php echo $base_path; ?>/interna.php/com_interna/list?prioridadcom_id=1&porProcesoCom=<?php echo md5(2)."&periodo_id=".$periodo_id; ?>">
                    <?php } ?>
                        <div class="tile-stats tile-red">            
                            <div class="icon"><i class="entypo-mail"></i></div>
                            <div class="num" data-start="0" data-end="<?php echo $internas_prufirmas ?>" data-postfix="" data-duration="1500" data-delay="0"><?php echo $internas_prufirmas ?></div>
                            <h3>Firmar Urgente</h3>
                        </div>
                    <?php if($internas_prufirmas) { ?>
                        </a>
                    <?php } ?>
                </div>
            <?php } ?>
            
            <?php if(false) { ?>
                <div class="col-sm-6 tooltip-primary" data-toggle="tooltip" data-original-title="Workflows que te han asignado.">
                    <?php if($workflow_interna) { ?>
                        <a href="<?php echo $base_path; ?>/interna.php/com_interna/list?porWorkflow=1&periodo_id=<?php echo $periodo_id; ?>">
                    <?php } ?>
                            <div class="tile-stats tile-purple">            
                                <div class="icon"><i class="entypo-shuffle"></i></div>
                                <div class="num" data-start="0" data-end="<?php echo $workflow_interna ?>" data-postfix="" data-duration="1500" data-delay="0"><?php echo $workflow_interna ?></div>
                                <h3>Workflows</h3>
                            </div>
                    <?php if($workflow_interna) { ?>
                        </a>
                    <?php } ?>
                </div>
            <?php } ?>
        </div>
    </div>
</div>

<!-- COMUNICACIONES ENVIADAS -->
<div class="row">
    <div class="col-sm-11 panel panel-gradient" data-collapsed="0" style="padding-left: 0px; padding-right: 0px; margin-left: 25px;">
        <div class="panel-heading">
            <div class="panel-title">
                Comunicaciones Externas Enviadas
            </div>			
            <div class="panel-options">
                <a href="#" data-rel="collapse"><i class="entypo-down-open"></i></a>				
            </div>
        </div>
        
        <div class="panel-body scrollcms" data-height="250" > 
            <?php if(!empty($enviadas_revisor)) { ?>
                <div class="col-sm-6 tooltip-primary" data-toggle="tooltip" data-original-title="Las que tienes por revisar.">
                    <?php if($enviadas_revisor) { ?>
                        <a href="<?php echo $base_path; ?>/enviada.php/com_enviada/list?porProcesoCom=<?php echo md5(4)."&periodo_id=".$periodo_id; ?>">
                    <?php } ?>
                    <div class="tile-stats tile-red">            
                        <div class="icon"><i class="entypo-mail"></i></div>
                        <div class="num" data-start="0" data-end="<?php echo $enviadas_revisor ?>" data-postfix="" data-duration="1500" data-delay="0"><?php echo $enviadas_revisor ?></div>
                        <h3>Por Revisar</h3>
                    </div>
                    <?php if($enviadas_revisor) { ?>
                        </a>
                    <?php } ?>
                </div>
            <?php } ?>
            
            <?php if(!empty($enviadas_gestor)) { ?>
                <div class="col-sm-6 tooltip-primary" data-toggle="tooltip" data-original-title="Las que tienes por gestionar.">
                    <?php if($enviadas_gestor) { ?>
                        <a href="<?php echo $base_path; ?>/enviada.php/com_enviada/list?porProcesoCom=<?php echo md5(3)."&periodo_id=".$periodo_id; ?>">
                    <?php } ?>
                    <div class="tile-stats tile-pink">            
                        <div class="icon"><i class="entypo-mail"></i></div>
                        <div class="num" data-start="0" data-end="<?php echo $enviadas_gestor ?>" data-postfix="" data-duration="1500" data-delay="0"><?php echo $enviadas_gestor ?></div>
                        <h3>Por Gestionar</h3>
                    </div>
                    <?php if($enviadas_gestor) { ?>
                        </a>
                    <?php } ?>
                </div>
            <?php } ?>
            
            <?php if(!empty($enviadas_firmar)) { ?>
                <div class="col-sm-6 tooltip-primary" data-toggle="tooltip" data-original-title="Las que tienes por firmar.">
                    <?php if($enviadas_firmar) { ?>
                        <a href="<?php echo $base_path; ?>/enviada.php/com_enviada/list?porProcesoCom=<?php echo md5(5)."&periodo_id=".$periodo_id; ?>">
                    <?php } ?>
                    <div class="tile-stats tile-cyan">            
                        <div class="icon"><i class="entypo-mail"></i></div>
                        <div class="num" data-start="0" data-end="<?php echo $enviadas_firmar ?>" data-postfix="" data-duration="1500" data-delay="0"><?php echo $enviadas_firmar ?></div>
                        <h3>Por Firmar</h3>
                    </div>
                    <?php if($enviadas_firmar) { ?>
                        </a>
                    <?php } ?>
                </div>
            <?php } ?>

            <?php if(!empty($enviadas_purfirmar)) { ?>
                <div class="col-sm-6 tooltip-primary" data-toggle="tooltip" data-original-title="Las que tienes por firmar urgentes.">
                    <?php if($enviadas_purfirmar) { ?>
                        <a href="<?php echo $base_path; ?>/enviada.php/com_enviada/list?porProcesoCom=<?php echo md5(5)."&periodo_id=".$periodo_id; ?>">
                    <?php } ?>
                    <div class="tile-stats tile-red">            
                        <div class="icon"><i class="entypo-mail"></i></div>
                        <div class="num" data-start="0" data-end="<?php echo $enviadas_purfirmar ?>" data-postfix="" data-duration="1500" data-delay="0"><?php echo $enviadas_purfirmar ?></div>
                        <h3>Firmar Urgente</h3>
                    </div>
                    <?php if($enviadas_purfirmar) { ?>
                        </a>
                    <?php } ?>
                </div>
            <?php } ?>

            <div class="col-sm-6 tooltip-primary" data-toggle="tooltip" data-original-title="Las que tu has firmado.">
                <?php if($enviadas) { ?>
                    <a href="<?php echo $base_path; ?>/enviada.php/com_enviada/list?porFunciSalida=1&periodo_id=<?php echo $periodo_id; ?>">
                <?php } ?>
                        <div class="tile-stats tile-orange">            
                            <div class="icon"><i class="entypo-mail"></i></div>
                            <div class="num" data-start="0" data-end="<?php echo $enviadas ?>" data-postfix="" data-duration="1500" data-delay="0"><?php echo $enviadas ?></div>
                            <h3>Enviadas</h3>
                        </div>
                <?php if($enviadas) { ?>
                    </a>
                <?php } ?>
            </div>
            
            <div class="col-sm-6 tooltip-primary" data-toggle="tooltip" data-original-title="Las que te enviaron copia.">
                <?php if($copia_informativa) { ?>
                    <a href="<?php echo $base_path; ?>/enviada.php/com_enviada/list?porFunciCopia=1&usuarioCopia=<?php echo $usuariologuiado."&periodo_id=".$periodo_id; ?>">
                <?php } ?>
                        <div class="tile-stats tile-blue">            
                            <div class="icon"><i class="entypo-mail"></i></div>
                            <div class="num" data-start="0" data-end="<?php echo $copia_informativa ?>" data-postfix="" data-duration="1500" data-delay="0"><?php echo $copia_informativa ?></div>
                            <h3>Copias</h3>
                        </div>
                <?php if($copia_informativa) { ?>
                    </a>
                <?php } ?>
            </div>
            
            <?php if(!empty($enviadas_gsalida)) { ?>
                <div class="col-sm-6 tooltip-primary" data-toggle="tooltip" data-original-title="Las que tienes por gestionar la notificaci&oacute;n.">
                    <?php if($enviadas_gsalida) { ?>
                        <a href="<?php echo $base_path; ?>/enviada.php/com_enviada/list?porGestSaldida=<?php echo md5($usuario->getPrimaryKey().'porGestSaldida')."&periodo_id=".$periodo_id; ?>">
                    <?php } ?>
                    <div class="tile-stats tile-cyan">            
                        <div class="icon"><i class="entypo-mail"></i></div>
                        <div class="num" data-start="0" data-end="<?php echo $enviadas_gsalida ?>" data-postfix="" data-duration="1500" data-delay="0"><?php echo $enviadas_gsalida ?></div>
                        <h3>Gesti&oacute;n Salida</h3>
                    </div>
                    <?php if($enviadas_gsalida) { ?>
                        </a>
                    <?php } ?>
                </div>
            <?php } ?>
        </div>
    </div>
</div>

<!-- ACTOS ADMINISTRATIVOS -->
<?php echo include_partial('infoActosAdm',array('periodo_id'=>$periodo_id, 'actosadm_data' => $actosadm_data,'usuario'=>$usuario)); ?>

<!-- MENSAJERIA -->
<?php echo include_partial('contadorServicios',array('periodo_id'=>$periodo_id, 'total_array_msj'=>$total_array_msj,'usuario'=>$usuario)); ?>

<!-- ARCHIVO -->
<div class="row">
    <div class="col-sm-11 panel panel-gradient" data-collapsed="0" style="padding-left: 0px; padding-right: 0px; margin-left: 25px;">
        <div class="panel-heading">
            <div class="panel-title">
                Prestamos Pendientes
            </div>			
            <div class="panel-options">
                <a href="#" data-rel="collapse"><i class="entypo-down-open"></i></a>				
            </div>
        </div>
        
        <div class="panel-body scrollcms" data-height="250" > 
            <div class="col-sm-6 tooltip-primary" data-toggle="tooltip" data-original-title="Expedientes que te han prestado y debes devolver.">
                <?php if($prestamos_pendientes) { ?>
                    <a href="<?php echo $base_path; ?>/archivo.php/prestamo/list?misPrestamos=1">
                <?php } ?>
                <div class="tile-stats tile-red">            
                    <div class="icon"><i class="entypo-suitcase"></i></div>
                    <div class="num" data-start="0" data-end="<?php echo $prestamos_pendientes ?>" data-postfix="" data-duration="1500" data-delay="0"><?php echo $prestamos_pendientes ?></div>
                    <h3>Prestados</h3>
                </div>
                <?php if($prestamos_pendientes) { echo "</a>"; } ?>
            </div>
        </div>
    </div>
</div>