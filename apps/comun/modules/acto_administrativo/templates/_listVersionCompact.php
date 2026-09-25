<?php
// Historial de versiones del contenido, para la barra lateral angosta de la pantalla de
// edición: filas compactas sin ícono (a diferencia de _listVersion.php, usado en la pestaña
// "Versiones" del detalle, donde sí hay espacio de sobra para tarjetas más grandes).
$path_theme = sfConfig::get('theme_simad');
$base_path = sfConfig::get('base_simad');
$currentUser = $sf_user->getAttribute('usuario_id', '', 'subscriber');
?>

<?php if (count($ldocument_version) == 0): ?>
    <div class="alert alert-default doc-version-compact-empty"><strong>No hay versiones disponibles</strong>.</div>
<?php else: ?>
    <div class="doc-version-compact-list">
        <?php foreach ($ldocument_version as $doc_row) { ?>
            <?php
                if ($doc_row->getStatusVersion() == StatusDocsVersion::Actual) {
                    $status_class = " status-current";
                } elseif ($doc_row->getStatusVersion() == StatusDocsVersion::Borrador) {
                    $status_class = " status-draft";
                } elseif ($doc_row->getStatusVersion() == StatusDocsVersion::Archivada) {
                    $status_class = " status-archived";
                } else {
                    $status_class = "";
                }
            ?>
            <div class="doc-version-compact-row<?php echo $doc_row->getCurrentVersion() ? " current" : ""; ?>" data-version-id="<?php echo $doc_row->getPrimaryKey(); ?>">
                <div class="doc-version-compact-main">
                    <span class="doc-version-compact-num">V<?php echo $doc_row->getVersionNumber(); ?></span>
                    <span class="status-badge<?php echo $status_class; ?>"><?php echo $doc_row->getStatusVersion(); ?></span>
                </div>
                <div class="doc-version-compact-meta">
                    <?php echo $doc_row->getFechaCreacion(); ?> — <?php echo $doc_row->getUsuario()->getNombreApellido(); ?>
                </div>
                <?php if ($doc_row->getCurrentVersion() !== 1) { ?>
                    <div class="doc-version-compact-actions">
                        <a class="doc-version-btn tooltip-primary" onclick="javascript:jQuery.OpenModalSIMAD('<?php echo url_for('acto_administrativo/compareVersion?docscontrolcambio_id=' . SED::encryption($doc_row->getPrimaryKey())); ?>');" data-toggle="tooltip" data-original-title="Comparar con versión actual">
                            <span class="glyphicon glyphicon-transfer"></span>
                        </a>
                        <a class="doc-version-btn tooltip-primary btntransfer" data-comptext="<?php echo SED::encryption($doc_row->getPrimaryKey()) ?>" data-endpoint="<?php echo url_for('acto_administrativo/transferVersion') ?>" data-toggle="tooltip" data-original-title="Restaurar esta versión">
                            <span class="glyphicon glyphicon-repeat"></span>
                        </a>
                    </div>
                <?php } ?>
            </div>
        <?php } ?>
    </div>
<?php endif; ?>
