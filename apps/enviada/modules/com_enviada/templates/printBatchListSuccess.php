<?php
$path_theme = sfConfig::get('theme_simad');
$base_path = sfConfig::get('base_simad');
use_helper('Object','jQuery');

$currentUser = $sf_user->getAttribute('username', '', 'subscriber') ;
$tplurl_download = $base_path.'/templates/PlantillaDescargaMasiva.xlsx';
?>

<?php if($cod_msg == 2) { ?>

    <div class="alert alert-success">
        Los radicados se enviaron para descarga satisfactoriamente!, por favor verifique que el archivo comprimido se descargo correctamente,
        si la descarga no inicia automaticamente por favor haga clic en el siguiente link 
        <a class="tooltip-primary" data-toggle="tooltip" data-original-title="Clic para descargar el archivo" target="_blank" href="<?php echo $url_download; ?>">Descargar archivo...</a>
    </div>

<?php }elseif($cod_msg == 1){ ?>

    <div class="alert alert-danger">
        Ocurrio un error  al procesar la solicitud y no se descargaron los radicados!, por favor intente de nuevo o comuniquese con el
        administrador del sistema <br /> <b><?php echo $msg_error ?></b>
    </div>

<?php } ?>

<div id="maein">
    <?php
    $cantidad_registros = count($sheetData);
    if(count($sheetData) == 0 || $process_end){
        echo form_tag('com_enviada/readFileExcel',array('name'=>'from1','multipart'=> true,'class' => 'form-horizontal form-groups-bordered validate'));    
    }else{
        echo form_tag('com_enviada/downloadBatchCom',array('name'=>'form1','class' => 'form-horizontal form-groups-bordered validate'));   
    }    
    ?>
    <div class="row">
    	<div class="col-md-12">
            <!-- Contenedor Pagina -->
            <div class="panel panel-gradient" data-collapsed="0">
                <div class="panel-heading">
                    <div class="panel-title">
                        Descargar Documentos
                    </div>
                </div>  
                <div class="form-group"></div>
                <?php
                  if(count($sheetData) == 0 || $process_end){
                    echo '<div class="form-group"><label for="lbremplazarcampo" class="col-sm-1 control-label">Archivo Datos</label><div class="col-sm-4">';
                    echo input_file_tag('file',array ('class' => 'upload', 'width' => '40'));
                    echo '</div>';
                    echo '<div class="col-sm-offset-1 col-sm-5">';
                    echo submit_tag('Enviar Archivo...',array('class'=>"btn btn-success tooltip-primary","data-toggle"=>"tooltip","data-original-title"=>"Debe cargar un archivo de Excel con tres columnas (Radicado,Remitente,Asunto) en ese mismo orden"));
                    echo '&nbsp;&nbsp;&nbsp;';
                    echo jq_button_to_function('Cancelar','javascript:parent.jQuery.CloseModalSIMAD();',array('class'=>"btn btn-red"));
					echo '&nbsp;&nbsp;&nbsp;';
                    echo jq_button_to_function('Descargar Plantilla','window.open("'.$tplurl_download.'", "_blank");;',array('target'=>'_new','class'=>"btn btn-blue"));					
                    echo '</div></div>';
                  }else{
                    echo input_hidden_tag('dataserialize',$sheetDataSerialize);
                    echo '<div class="form-group"><div class="col-sm-offset-1 col-sm-8">';
                    echo submit_tag('Descargar Radicados...',array('class'=>"btn btn-success"));
                    echo '&nbsp;&nbsp;&nbsp;';
                    echo button_to('Regresar...','com_enviada/printBatchList',array('class'=>"btn btn-blue"));
                    echo '&nbsp;&nbsp;&nbsp;';
                    echo jq_button_to_function('Cancelar','javascript:parent.jQuery.CloseModalSIMAD();',array('class'=>"btn btn-red"));
                    echo '</div></div>';
                  }
                ?>
            </div>
        </div>
    </div>
</div>

<div id="remoteupdate">
    <?php
    $print_header = 0;
    $count_header = 0;
    $count_update = count($results_update);
    ?>
    <div class="row">
    	<div class="col-md-12">
    		<?php $cantidad_registros = count($sheetData); ?>
            <div id="tablelist" >
        	    <?php if($cantidad_registros == 0): ?>
                    <div class="panel panel-primary">
                        <div class="panel-heading">
                            <div class="panel-title">Radicados para descargar</div>
                        </div>
            
                        <div class="panel-body">
                            <div class="alert alert-default"><strong>No existen Registros</strong>, Intente cargar el archivo nuevamente.</div>
                        </div>
                    </div>
        	    <?php else: ?>
        		    <div class="panel panel-primary">                
        		      <div class="panel-heading">
        		        <div class="panel-title">Radicados para descargar</div>                
        		      </div>
                    <div class="panel-body with-table">
        		        <table class="table table-bordered table-hover table-striped responsive" id="table-1">
        		          <thead>
        		            <tr>
        		            	<th data-hide="phone" class="text-center" style="width: 5%;">Item</th>
        						<th class="text-center" style="width: 15%;">Radicado</th>
                                <th data-hide="phone" class="text-center" style="width: 25%;">Remitente</th>
                                <th data-hide="phone" class="text-center">Asunto</th>
                                <th class="text-center" style="width: 5%;">Status</th>
        					</tr>
        		          </thead>
        		          <tbody>
                              <?php $print_header = false; $item_count = 0; ?>
                              <?php foreach($sheetData as $data){ ?>
                                  <?php if($print_header){ ?>
                                      <?php if(trim($data["A"])){ ?>
                                          <tr>
                                            <td class="text-center"><?php echo ++$item_count ?></td>
                                            <?php foreach($data as $clave => $valor){ ?>
                                                <?php if($clave == "D"){ ?>
                                                    <td class="text-center">
                                                        <div id="<?php echo md5($item_count) ?>">
                                                            <?php 
                                                                if($valor == "OK") { 
                                                                    echo image_tag('simad/ico_check_green.png', array('width'=>"25","class"=>"tooltip-primary","data-toggle"=>"tooltip","data-original-title"=>"El archivo se genero correctamente"));
                                                                }else{
                                                                    echo image_tag('simad/ico_check_red.png', array('width'=>"25","class"=>"tooltip-primary","data-toggle"=>"tooltip","data-original-title"=>"Error al generar el archivo"));
                                                                }
                                                            ?>
                                                        </div>
                                                    </td>
                                                <?php }else{ ?>
                                                    <td><?php echo $valor; ?></td>
                                                <?php } ?>
                                            <?php } ?>

                                            <?php if(!isset($data["D"])) { ?>
                                                <td class="text-center">
                                                    <div id="<?php echo md5($item_count) ?>">
                                                        <?php echo image_tag('simad/ico_check_red.png', array('width'=>"25","class"=>"tooltip-primary","data-toggle"=>"tooltip","data-original-title"=>"Pendiente por descargar")); ?>
                                                    </div>
                                                </td>
                                            <?php } ?>

                                          </tr>
                                      <?php } ?>
                                  <?php }else{ $print_header = true; } ?>
                              <?php } ?>
                          </tbody>
    		            </table>
                    </div>
                  </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    <?php if(!empty($url_download)){ ?>
        window.open("<?php echo $url_download; ?>", "_blank");
    <?php } ?>
</script>