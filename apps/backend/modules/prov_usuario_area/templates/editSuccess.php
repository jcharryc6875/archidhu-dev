<?php use_helper('Object', 'Validation', 'ObjectAdmin', 'I18N', 'Date') ?>

<?php use_stylesheet('/sf/sf_admin/css/main') ?>

<div id="sf_admin_container">

<h1><?php echo __('edit prov_usuario_area', 
array()) ?></h1>

<div id="sf_admin_header">
<?php include_partial('prov_usuario_area/edit_header', array('prov_usuario_area' => $prov_usuario_area)) ?>
</div>

<div id="sf_admin_content">
<?php include_partial('prov_usuario_area/edit_messages', array('prov_usuario_area' => $prov_usuario_area, 'labels' => $labels)) ?>
<?php include_partial('prov_usuario_area/edit_form', array('prov_usuario_area' => $prov_usuario_area, 'labels' => $labels)) ?>
</div>

<div id="sf_admin_footer">
<?php include_partial('prov_usuario_area/edit_footer', array('prov_usuario_area' => $prov_usuario_area)) ?>
</div>

</div>
