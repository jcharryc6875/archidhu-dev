<?php
$path_theme = sfConfig::get('theme_simad');
$base_path = sfConfig::get('base_simad');

use_helper('Object');
use_helper('jQuery');
?>
<script type="text/javascript">
	parent.jQuery.RecargarPagina();
</script>
<div class="row">
	<div class="col-md-12">
		<div class="panel panel-primary">
		      <div class="panel-heading">
		        <div class="panel-title">Adicionar Solicitud</div>
		      </div>

		      <div class="panel-body">
		      	<div class="alert alert-default"><strong>La solicitud de Prestamo fue Rechazada.</strong>.</div>
		      	<a class="btn btn-white btn-sm tooltip-primary" data-toggle="tooltip" data-original-title="Cerrar registro" href="#" onclick="javascript:parent.jQuery.ReloadAndCloseModalSIMAD();">
                    <img src="<?php echo $base_path; ?>/images/simad/ico_cerrar.png" title="" width="25" align="middle" />Cerrar
                </a>
	      	  </div>
      	</div>
  	</div>
</div>
<script type="text/javascript">
javascript:parent.jQuery.ReloadAndCloseModalSIMAD();
</script>