<?php use_helper('Validation') ?>
<?php echo "<pre>" ?>
<?php //var_dump($sf)  ?>
<?php echo "</pre>" ?>

<?php echo form_tag('security/passwd') ?>
 
  <fieldset>
 
  <div class="form-row">
    <label for="username">Nombre usuario:</label>
    <?php echo form_error('username') ?>
    <?php echo input_tag('username', $sf_user->getAttribute('username', '', 'subscriber')  ) ?>
  </div>
 
  <div class="form-row">
    <label for="oldpassword">Clave antigua:</label>
    <?php echo form_error('oldpassword') ?>
    <?php echo input_password_tag('oldpassword') ?>
  </div>

  <div class="form-row">
    <label for="password">Clave Nueva:</label>
    <?php echo form_error('newpassword1') ?>
    <?php echo input_password_tag('newpassword1') ?>
  </div> 

  <div class="form-row">
    <label for="password">Repita Clave Nueva:</label>
    <?php echo form_error('newpassword2') ?>
    <?php echo input_password_tag('newpassword2') ?>
  </div> 

  </fieldset>
 
  <?php echo input_hidden_tag('referer', $sf_request->getAttribute('referer')) ?>
  <?php echo submit_tag('cambiar') ?>
 
</form>

