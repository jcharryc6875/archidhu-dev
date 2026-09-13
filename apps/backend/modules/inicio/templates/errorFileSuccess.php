<?php
$path_theme = sfConfig::get('theme_simad');
$base_path = sfConfig::get('base_simad');
?>
<!-- Google font -->
<link href="https://fonts.googleapis.com/css?family=Cabin:400,700" rel="stylesheet">
<div id="notfound">
	<div class="notfound">
		<div class="notfound-404">
			<div></div>
			<h1>Opps</h1>
		</div>
		<h2>El recurso no existe</h2>
		<p><strong>Hola!!,</strong> el archivo solicitado no esta disponible.</p>
        <a style="cursor: pointer;" onclick="javascript:jQuery.FancyBoxIsOpen('<?php echo $base_path; ?>/index.php');">Ir Pagina Principal</a>
	</div>
</div>