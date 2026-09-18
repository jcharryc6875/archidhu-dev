<?php
// UARIV-202605 (ampliación): comparación visual, página a página, de dos versiones del Word
?>
<div class="row">
  <div class="col-md-12">
    <div class="panel panel-gradient" data-collapsed="0">
      <div class="panel-heading">
        <div class="panel-title">
          Comparar versiones de Word — Acto Administrativo #<?php echo $acto_administrativo->getPrimaryKey(); ?>
        </div>
      </div>
      <div class="panel-body">
        <?php if($huboErrorConversion){ ?>
          <div class="alert alert-danger">
            <strong>Aviso:</strong> no fue posible generar la vista previa de una o ambas versiones (el archivo pudo haberse movido/eliminado del servidor, o LibreOffice no pudo convertirlo). Contacte al administrador si el problema persiste.
          </div>
        <?php } ?>
        <div class="row">
          <div class="col-sm-6">
            <h4>Versión <?php echo $versionA->getVersionNumber(); ?><?php echo $versionA->getCurrentVersion() ? ' (actual)' : ''; ?></h4>
            <p class="text-muted">
              <?php echo $versionA->getFechaCreacion(); ?> —
              <?php echo $versionA->getUsuario() ? $versionA->getUsuario()->getNombreAll() : ''; ?>
            </p>
            <?php if(count($imagenesA) == 0){ ?>
              <div class="alert alert-default">No se pudo generar la vista previa de esta versión.</div>
            <?php } ?>
            <?php foreach ($imagenesA as $imagen): ?>
              <img src="<?php echo $imagen; ?>" class="img-responsive img-thumbnail" style="margin-bottom: 10px;" alt="Página de la versión <?php echo $versionA->getVersionNumber(); ?>">
            <?php endforeach; ?>
          </div>
          <div class="col-sm-6">
            <h4>Versión <?php echo $versionB->getVersionNumber(); ?><?php echo $versionB->getCurrentVersion() ? ' (actual)' : ''; ?></h4>
            <p class="text-muted">
              <?php echo $versionB->getFechaCreacion(); ?> —
              <?php echo $versionB->getUsuario() ? $versionB->getUsuario()->getNombreAll() : ''; ?>
            </p>
            <?php if(count($imagenesB) == 0){ ?>
              <div class="alert alert-default">No se pudo generar la vista previa de esta versión.</div>
            <?php } ?>
            <?php foreach ($imagenesB as $imagen): ?>
              <img src="<?php echo $imagen; ?>" class="img-responsive img-thumbnail" style="margin-bottom: 10px;" alt="Página de la versión <?php echo $versionB->getVersionNumber(); ?>">
            <?php endforeach; ?>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
