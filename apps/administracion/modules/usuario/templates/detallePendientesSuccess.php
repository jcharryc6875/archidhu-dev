<?php
$path_theme = sfConfig::get('theme_simad');
$base_path = sfConfig::get('base_simad');
$currentUser = $sf_user->getAttribute('usuario_id', '', 'subscriber') ;
use_helper('jQuery');
?>

<div class="row">
	<div class="col-md-12">
      <?php

        $cantidad_registros = $pager->getNbResults();
        if($cantidad_registros == 0):	      
	    ?>

          <div class="panel panel-primary">
            <div class="panel-heading">
              <div class="panel-title">Lista de Registros Pendientes</div>
            </div>

            <div class="panel-body">
              <div class="alert alert-default"><strong>No existen Registros Pendientes</strong></div>
              </div>
          </div>

      <?php
	      else:
          $laClase = $pager->getClassPeer();

          switch($laClase)
          {
            case 'ComRecibidaPeer':
              echo include_partial('tablaComRecibida', array('pager' => $pager, 'parametros' => $parametros));
              break;

            case 'ComInternaPeer':
              echo include_partial('tablaComInterna', array('pager' => $pager, 'parametros' => $parametros));
              break;

            case 'ComEnviadaPeer':
              echo include_partial('tablaComEnviada', array('pager' => $pager, 'parametros' => $parametros));
              break;
          }
	    ?>

      <?php
        endif;
      ?>
	</div>
</div>