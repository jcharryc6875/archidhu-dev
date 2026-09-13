<?php use_helper('Validation') ?>


<div id="sf_admin_container">

<h1>Recuperar Clave</h1>

<?php echo form_tag('security/recuperar') ?>
 
  <fieldset>
 
  <div class="form-row">
    <label for="username">Nombre usuario:</label>
    <?php echo form_error('username') ?>

    <?php echo input_tag('username', $sf_params->get('username')) ?>
  </div>
 
  <div class="form-row">
    <label for="password">correo Registrado:</label>
    <?php echo form_error('email') ?>

    <?php echo input_tag('email') ?>
  </div>
 
  </fieldset>
 
  <?php echo input_hidden_tag('referer', $sf_request->getAttribute('referer')) ?>
  <?php echo submit_tag('Recuperar') ?>
 
</form>
<?php //echo link_to('Olvide mi clave','security/recuperar') ?>

</div>