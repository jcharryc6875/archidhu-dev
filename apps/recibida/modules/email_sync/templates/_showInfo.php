<?php 
$path_theme = sfConfig::get('theme_simad');
$base_path = sfConfig::get('base_simad');
$dir_typeicons =  $path_theme.'assets/images/FileTypes/';

use_helper('Object','jQuery');
?>
<?php if ($sf_user->hasFlash('messages_info')): ?>
    <div class="alert alert-success"><strong>Excelente! </strong><?php echo $sf_user->getFlash('messages_info') ?></div>
<?php endif ?>
<?php if ($sf_user->hasFlash('messages_error')): ?>
    <div class="alert alert-danger"><strong>Opps! </strong><?php echo $sf_user->getFlash('messages_error') ?></div>
<?php endif ?>
<!-- Informacion Detalle -->
<div class="col-sm-12 col-md-12">
	<div class="mail-body">
		<div class="mail-header">
			<div class="mail-title">
				<?php echo $email_message->getAsunto(); ?>
			</div>
		</div>
				
		<div class="mail-info">
			<div class="mail-sender dropdown">
				<a href="#" class="dropdown-toggle" data-toggle="dropdown">
					<img src="<?php echo $base_path ?>/images/logo_users.png" class="img-circle" width="32" /> 
					<span style="padding-bottom:5px"><strong>De:</strong> <span class="mail-row-item"><?php echo $email_message->getEmailOrigen(); ?></span></span>
					<br><br>
					<span style="padding-bottom:5px"><strong>Fecha recibido:</strong> <span class="mail-row-item"><?php echo $email_message->getFechaRecibido(); ?></span></span>
					<br><br>
					<span style="padding-bottom:5px"><strong>Para:</strong> <span class="mail-row-item"><?php echo htmlspecialchars($email_message->getEmailDestino()); ?></span></span>
					<br><br>
					<span style="padding-bottom:5px"><strong>Asunto:</strong> <span class="mail-row-item"><?php echo $email_message->getAsunto(); ?></span></span>
				</a>
			</div>
			
			<div class="mail-date">
				<?php echo $email_message->getFechaRecibido(); ?>
			</div>
			
			</div>
				<div class="mail-text" id="correo-contenido"></div>
				<?php if($email_message->getCountAttachment() > 0){ ?>
					<div class="mail-attachments">
						<h4>
							<i class="entypo-attach"></i> Attachments <span>(<?php echo $email_message->getCountAttachment(); ?>)</span>
						</h4>
						<div class="attachment-item">
							<ul>
								<?php foreach ($email_message->getEmailAttachments() as $attach) { ?>
									<?php 
										$url_async = $attach->getUrlTokenViewImageByObjectAjax();
										$type_file = $dir_typeicons.$attach->getExtension().".svg";
										$path_icon = sfConfig::get('sf_web_dir').$type_file;
										$icon_file = file_exists($path_icon) ? $type_file : $dir_typeicons."unknow.svg"; 
									?>
									<li>
										<a href="#" class="thumb" style="cursor: not-allowed;">
											<img style="margin-left:35%;width:40px;" src="<?php echo $icon_file ?>" class="img-rounded" />
										</a>
										
										<a href="#" class="name" style="cursor: not-allowed;">
											<?php echo $attach->getFilename(); ?>
										</a>
										
										<div class="links">
											<a href="#" onclick="<?php 
                                                    echo jq_remote_function(array(
                                                        'update'  => null,
                                                        'url'     => $base_path.url_for($url_async['baseurl']),
                                                        'with'    => "'key_id=".$url_async['key_id']."&vtoken=".$url_async['vtoken']."'",
                                                        'loading' => "javascript:jQuery.LoadingStructData();",
                                                        'complete' => "javascript:jQuery.CloseLoadingStructData(); try{ var response_value = JSON.parse(XMLHttpRequest.responseText); if(response_value.status == 200){ toastr.success(response_value.message); window.open(response_value.url_file, 'MyWindow'); }else{ toastr.error(response_value.message); } }catch(err) { toastr.error(err.message); }",
                                                    ));
                                                    ?>">Visualizar</a>
											<span style="text-aling:right"><strong><?php echo !empty($attach->getSizeFile()) ? number_format(($attach->getSizeFile()/1024), 2) : 0; ?></strong> KiB</span>
										</div>
									</li>
								<?php } ?>
							</ul>
						</div>
					</div>
				<?php } ?>
			</div>
		</div>
	</div>	
</div>

<script type="text/javascript">
	document.addEventListener("DOMContentLoaded", function () {
		let htmlCorreo = `<?php echo trim($email_message->getContenido()); ?>`;

		// Parsear el HTML
		let parser = new DOMParser();
		let doc = parser.parseFromString(htmlCorreo, "text/html");

		// Eliminar <script> y <style>
		doc.querySelectorAll("script").forEach(el => el.remove());

		// Insertar contenido limpio
		document.getElementById("correo-contenido").appendChild(doc.body);
	});
</script>