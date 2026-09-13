<?php
$path_theme = sfConfig::get('theme_simad');
$base_path = sfConfig::get('base_simad');

$usuariologuiado = $sf_user->getAttribute('usuario_id','', 'subscriber');
use_helper('Object','jQuery','UserComponent');
?>
<!-- ACTOS ADMINISTRATIVOS -->
<div class="row">
    <div class="col-sm-11 panel panel-gradient" data-collapsed="0" style="padding-left: 0px; padding-right: 0px; margin-left: 25px;">
        <div class="panel-heading">
            <div class="panel-title">
                Actos Administrativos
            </div>			
            <div class="panel-options">
                <a href="#" data-rel="collapse"><i class="entypo-down-open"></i></a>				
            </div>
        </div>
        
        <div class="panel-body scrollcms" data-height="250" >

            <?php if(isset($actosadm_data['actoadm_leer']) && !empty($actosadm_data['actoadm_leer'])) { ?>
                <div class="col-sm-6 tooltip-primary" data-toggle="tooltip" data-original-title="Las que tienes por leer.">
                    <?php if($actosadm_data['actoadm_leer']) { ?>
                        <a href="<?php echo $base_path; ?>/comun.php/acto_administrativo/list?actoadministrativo_id=2&porFunciEntrada=1&periodo_id=<?php echo $periodo_id; ?>">
                    <?php } ?>
                            <div class="tile-stats tile-plum">            
                                <div class="icon"><i class="entypo-mail"></i></div>
                                <div class="num" data-start="0" data-end="<?php echo $actosadm_data['actoadm_leer'] ?>" data-postfix="" data-duration="1500" data-delay="0"><?php echo $actosadm_data['actoadm_leer'] ?></div>
                                <h3>Por Leer</h3>
                            </div>
                    <?php if($actosadm_data['actoadm_leer']) { ?>
                        </a>
                    <?php } ?>
                </div>
            <?php } ?>

            <?php if(isset($actosadm_data['actoadm_revisor']) && !empty($actosadm_data['actoadm_revisor'])) { ?>
                <div class="col-sm-6 tooltip-primary" data-toggle="tooltip" data-original-title="Los actos administrativos que tienes por revisar.">
                    <?php if($actosadm_data['actoadm_revisor']) { ?>
                        <a href="<?php echo $base_path; ?>/comun.php/acto_administrativo/list?porProcesoCom=<?php echo md5(4)."&periodo_id=".$periodo_id; ?>">
                    <?php } ?>
                    <div class="tile-stats tile-red">            
                        <div class="icon"><i class="entypo-mail"></i></div>
                        <div class="num" data-start="0" data-end="<?php echo $actosadm_data['actoadm_revisor'] ?>" data-postfix="" data-duration="1500" data-delay="0"><?php echo $actosadm_data['actoadm_revisor'] ?></div>
                        <h3>Por Revisar</h3>
                    </div>
                    <?php if($actosadm_data['actoadm_revisor']) { ?>
                        </a>
                    <?php } ?>
                </div>
            <?php } ?>
            
            <?php if(isset($actosadm_data['actoadm_gestionar']) && !empty($actosadm_data['actoadm_gestionar'])) { ?>
                <div class="col-sm-6 tooltip-primary" data-toggle="tooltip" data-original-title="Los actos administrativos que tienes por gestionar.">
                    <?php if($actosadm_data['actoadm_gestionar']) { ?>
                        <a href="<?php echo $base_path; ?>/comun.php/acto_administrativo/list?porProcesoCom=<?php echo md5(3)."&periodo_id=".$periodo_id; ?>">
                    <?php } ?>
                    <div class="tile-stats tile-pink">            
                        <div class="icon"><i class="entypo-mail"></i></div>
                        <div class="num" data-start="0" data-end="<?php echo $actosadm_data['actoadm_gestionar'] ?>" data-postfix="" data-duration="1500" data-delay="0"><?php echo $actosadm_data['actoadm_gestionar'] ?></div>
                        <h3>Por Gestionar</h3>
                    </div>
                    <?php if($actosadm_data['actoadm_gestionar']) { ?>
                        </a>
                    <?php } ?>
                </div>
            <?php } ?>
            
            <?php if(isset($actosadm_data['actoadm_firmar']) && !empty($actosadm_data['actoadm_firmar'])) { ?>
                <div class="col-sm-6 tooltip-primary" data-toggle="tooltip" data-original-title="Las que tienes pendientes por firmar.">
                    <?php if($actosadm_data['actoadm_firmar']) { ?>
                        <a href="<?php echo $base_path; ?>/comun.php/acto_administrativo/list?porProcesoCom=<?php echo md5(2)."&periodo_id=".$periodo_id; ?>">
                    <?php } ?>
                    <div class="tile-stats tile-cyan">            
                        <div class="icon"><i class="entypo-mail"></i></div>
                        <div class="num" data-start="0" data-end="<?php echo $actosadm_data['actoadm_firmar'] ?>" data-postfix="" data-duration="1500" data-delay="0"><?php echo $actosadm_data['actoadm_firmar'] ?></div>
                        <h3>Por Firmar</h3>
                    </div>
                    <?php if($actosadm_data['actoadm_firmar']) { ?>
                        </a>
                    <?php } ?>
                </div>
            <?php } ?>

            <?php if(isset($actosadm_data['actoadm_purfirmar']) && !empty($actosadm_data['actoadm_purfirmar'])) { ?>
                <div class="col-sm-6 tooltip-primary" data-toggle="tooltip" data-original-title="Las que tienes por firmar urgentes.">
                    <?php if($actosadm_data['actoadm_purfirmar']) { ?>
                        <a href="<?php echo $base_path; ?>/comun.php/acto_administrativo/list?porProcesoCom=<?php echo md5(2)."&prioridadcom_id=1&periodo_id=".$periodo_id; ?>">
                    <?php } ?>
                    <div class="tile-stats tile-red">            
                        <div class="icon"><i class="entypo-mail"></i></div>
                        <div class="num" data-start="0" data-end="<?php echo $actosadm_data['actoadm_purfirmar'] ?>" data-postfix="" data-duration="1500" data-delay="0"><?php echo $actosadm_data['actoadm_purfirmar'] ?></div>
                        <h3>Firmar Urgente</h3>
                    </div>
                    <?php if($actosadm_data['actoadm_purfirmar']) { ?>
                        </a>
                    <?php } ?>
                </div>
            <?php } ?>

            <div class="col-sm-6 tooltip-primary" data-toggle="tooltip" data-original-title="Las actos administrativos que tu has firmado.">
                <?php if(isset($actosadm_data['actoadm_firmadas']) && !empty($actosadm_data['actoadm_firmadas'])) { ?>
                    <a href="<?php echo $base_path; ?>/comun.php/acto_administrativo/list?porFunciSalida=1&periodo_id=<?php echo $periodo_id; ?>">
                <?php } ?>
                        <div class="tile-stats tile-orange">            
                            <div class="icon"><i class="entypo-mail"></i></div>
                            <div class="num" data-start="0" data-end="<?php echo $actosadm_data['actoadm_firmadas'] ?>" data-postfix="" data-duration="1500" data-delay="0"><?php echo $actosadm_data['actoadm_firmadas'] ?></div>
                            <h3>Firmados</h3>
                        </div>
                <?php if($actosadm_data['actoadm_firmadas']) { ?>
                    </a>
                <?php } ?>
            </div>
            
            <?php if(isset($actosadm_data['actoadm_infcopia']) && !empty($actosadm_data['actoadm_infcopia'])) { ?>
                <div class="col-sm-6 tooltip-primary" data-toggle="tooltip" data-original-title="Los actos administrativos que te enviaron como copia.">
                    <?php if($actosadm_data['actoadm_infcopia']) { ?>
                        <a href="<?php echo $base_path; ?>/comun.php/acto_administrativo/list?porFunciCopia=1&periodo_id="<?php echo $periodo_id; ?>">
                    <?php } ?>
                        <div class="tile-stats tile-blue">            
                            <div class="icon"><i class="entypo-mail"></i></div>
                            <div class="num" data-start="0" data-end="<?php echo $actosadm_data['actoadm_infcopia'] ?>" data-postfix="" data-duration="1500" data-delay="0"><?php echo $actosadm_data['actoadm_infcopia'] ?></div>
                            <h3>Copias</h3>
                        </div>
                    <?php if($actosadm_data['actoadm_infcopia']) { ?>
                        </a>
                    <?php } ?>
                </div>
            <?php } ?>

            <?php if(isset($actosadm_data['actoadm_gsalida']) && !empty($actosadm_data['actoadm_gsalida'])) { ?>
                <div class="col-sm-6 tooltip-primary" data-toggle="tooltip" data-original-title="Los actos administrativos que tienes por gestionar la notificaci&oacute;n.">
                    <?php if($actosadm_data['actoadm_gsalida']) { ?>
                        <a href="<?php echo $base_path; ?>/comun.php/acto_administrativo/list?porGestSaldida=<?php echo md5($usuario->getPrimaryKey().'porGestSaldida')."&periodo_id=".$periodo_id; ?>">
                    <?php } ?>
                    <div class="tile-stats tile-cyan">            
                        <div class="icon"><i class="entypo-mail"></i></div>
                        <div class="num" data-start="0" data-end="<?php echo $actosadm_data['actoadm_gsalida'] ?>" data-postfix="" data-duration="1500" data-delay="0"><?php echo $actosadm_data['actoadm_gsalida'] ?></div>
                        <h3>Gesti&oacute;n Salida</h3>
                    </div>
                    <?php if($actosadm_data['actoadm_gsalida']) { ?>
                        </a>
                    <?php } ?>
                </div>
            <?php } ?>
        </div>
    </div>
</div>