<?php
    include_once(dirname(__FILE__).'/../../../../lib/helper/ModalHelper.php');
    if($includeDiv)
    {
?>
<div class="col-md-8" id="<?php echo md5('strlistinfo');?>">
<?php 
    }
?>
        <div class="panel panel-success">
                <div class="panel-heading">
                    <div class="panel-title">Lista de Resultados de la Consulta</div>
                </div>
                    <?php 
                    if(empty($com_recibidas))
                    { 
                        ?>
                                <div class="panel-body">
                                    <div class="alert alert-default"><strong>No existen Registros</strong>, Intente con diferentes filtros de consulta.</div>
                                </div>
                        <?php
                    }
                    else
                    {
                        ?>
                        <!-- Informacion Detalle -->
                        <hr />
                            <div class="col-sm-12 col-md-12">  
                                <div class="row">					
                                    <div class="col-sm-6">
                                        <div class="col-sm-4"><p><strong>Radicado:</strong></p></div>
                                        <div class="col-sm-8">
                                            <p>
                                                <?php 
                                                    echo $com_recibidas['RADICADO'] . '  ';
                                                    echo show_modal_files($com_recibidas['ADJUNTOS'], 'ico_ver_adj', null, 'DIGIT_COM');
                                                ?>
                                            </p>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        
                                    </div>
                                </div>

                                <div class="row">					
                                    <div class="col-sm-6">
                                        <div class="col-sm-4"><p><strong>Dependencia Asignada:</strong></p></div>
                                        <div class="col-sm-8">
                                            <p><?php echo $com_recibidas['DEPENDENCIA_ASIGNADA']; ?></p>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                    </div>
                                </div>

                                <div class="row">					
                                    <div class="col-sm-6">
                                        <div class="col-sm-4"><p><strong>Dependencia Radicador:</strong></p></div>
                                        <div class="col-sm-8">
                                            <p><?php echo $com_recibidas['DEPENDENCIA_RADICADOR']; ?></p>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="col-sm-4"><p><strong>Anexos:</strong></p></div>
                                        <div class="col-sm-8">
                                            <?php 
                                            echo show_modal_files($com_recibidas['ADJUNTOS'], 'ico-adj-file', null, 'ANEXO');
                                            ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <hr />
                        <div class="panel-body">
                            <!-- burbujas -->
                            <?php 
                                include('_customBubbles.php');
                            ?>
                                     
                        </div>
                    <?php 
                        if(!empty($comenviada_id))
                        {
                            ?> 
                                <!-- SEGUNDA PARTE -->
                                <div class="panel-info" >
                                        <div class="panel-heading">
                                            <div class="panel-title">Respuesta Asociada</div>
                                        </div>
                                        <div style="width:100% clear:both; height:30px;"></div>
                                            <div class="col-sm-12 col-md-12">
                                                <!--  CABEZERAS  -->
                                                            <div class="row">					
                                                                <div class="col-sm-6">
                                                                <div class="col-sm-4"><p><strong>Radicado:</strong></p></div>
                                                                <div class="col-sm-8">
                                                                        <p>
                                                                            <?php 
                                                                                echo $com_enviada->getRadicado() . '  ';
                                                                                echo show_modal_files($anexos_com_enviada, 'ico_ver_adj', null, 'DIGIT_COM');
                                                                            ?>
                                                                        </p>
                                                                </div>
                                                                </div>
                                                                <div class="col-sm-6">
                                                                    <div class="col-sm-5"><p><strong>Asunto:</strong></p></div>
                                                                    <div class="col-sm-7"><p><?php echo $com_enviada->getAsunto(); ?></p></div>
                                                                </div>
                                                            </div>
                                                            <div class="row">					
                                                                <div class="col-sm-6">
                                                                <div class="col-sm-4"><p><strong>Copias:</strong></p></div>
                                                                <div class="col-sm-8">
                                                                    <p><?php echo $bitac_com_enviada['copias']; ?></p>
                                                                </div>
                                                                </div>
                                                                <div class="col-sm-6">
                                                                
                                                                </div>
                                                            </div>
                                                            <div class="row">					
                                                                    <div class="col-sm-6">
                                                                    <div class="col-sm-4"><p><strong>Dependencia Radicador:</strong></p></div>
                                                                    <div class="col-sm-8">
                                                                        <p><?php echo $bitac_com_enviada['dependencia_radicador']; ?></p>
                                                                    </div>
                                                                    </div>
                                                                    <div class="col-sm-6">
                                                                    <div class="col-sm-4"><p><strong>Anexos:</strong></p></div>
                                                                    <div class="col-sm-8">
                                                                        <?php  
                                                                        echo show_modal_files($anexos_com_enviada, 'ico-adj-file', null, 'ANEXO');
                                                                        ?>
                                                                    </div>
                                                            </div>
                                                <!--  TABLA  -->
                                                <div style="width:100% clear:both; height:50px;"></div>
                                                <div class="panel-body">
                                                </div>
                                            </div>
                                        </div>
                                </div>
                            <?php 
                        }
                    ?> 
         <?php
                    }
            ?>
        </div>
<?php 
    if($includeDiv)
    {
?>
</div> 
<?php 
    }
?>