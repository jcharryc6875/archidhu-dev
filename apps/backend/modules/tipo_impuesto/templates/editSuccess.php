<?php use_helper('Object', 'Validation', 'ObjectAdmin', 'I18N', 'Date') ?>

<?php use_stylesheet('/sf/sf_admin/css/main') ?>

<div id="sf_admin_container">

<h1><?php echo __('edit tipo_impuesto', 
array()) ?></h1>

<div id="sf_admin_header">
<?php include_partial('tipo_impuesto/edit_header', array('tipo_impuesto' => $tipo_impuesto)) ?>
</div>

<div id="sf_admin_content">
<?php include_partial('tipo_impuesto/edit_messages', array('tipo_impuesto' => $tipo_impuesto, 'labels' => $labels)) ?>
<?php include_partial('tipo_impuesto/edit_form', array('tipo_impuesto' => $tipo_impuesto, 'labels' => $labels)) ?>
</div>

<div id="sf_admin_footer">
<?php include_partial('tipo_impuesto/edit_footer', array('tipo_impuesto' => $tipo_impuesto)) ?>
</div>

</div>
