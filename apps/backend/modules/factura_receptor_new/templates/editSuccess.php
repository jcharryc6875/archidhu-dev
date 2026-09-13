<?php use_helper('Object', 'Validation', 'ObjectAdmin', 'I18N', 'Date') ?>

<?php use_stylesheet('/sf/sf_admin/css/main') ?>

<div id="sf_admin_container">

<h1><?php echo __('edit factura_receptor', 
array()) ?></h1>

<div id="sf_admin_header">
<?php include_partial('factura_receptor/edit_header', array('factura_receptor' => $factura_receptor)) ?>
</div>

<div id="sf_admin_content">
<?php include_partial('factura_receptor/edit_messages', array('factura_receptor' => $factura_receptor, 'labels' => $labels)) ?>
<?php include_partial('factura_receptor/edit_form', array('factura_receptor' => $factura_receptor, 'labels' => $labels)) ?>
</div>

<div id="sf_admin_footer">
<?php include_partial('factura_receptor/edit_footer', array('factura_receptor' => $factura_receptor)) ?>
</div>

</div>
