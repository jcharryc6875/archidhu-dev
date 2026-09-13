<?php use_helper('Validation') ?>


<?php echo form_tag('login') ?>
 
  <fieldset>
 
  <div class="form-row">
    <label for="username">Nombre usuario:</label>
    <?php echo form_error('username') ?>

    <?php echo input_tag('username', $sf_params->get('username')) ?>
  </div>
 
  <div class="form-row">
    <label for="password">password:</label>
    <?php echo form_error('password') ?>

    <?php echo input_password_tag('password') ?>
  </div>
 
  </fieldset>
 
  <?php echo input_hidden_tag('referer', $sf_request->getAttribute('referer')) ?>
  <?php echo submit_tag('sign in') ?>
 
</form>
<?php echo link_to('Olvide mi clave','security/recuperar') ?>

