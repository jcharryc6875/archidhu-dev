<?php use_helper('Object') ?>
<?php if($directorio_extended != null) { ?>
  <div class="form-group">
    <!-- Prefijo -->
    <label for="lbprefijo" class="col-sm-1 control-label">Prefijo:</label>
    <div class="col-sm-3">
      <?php
          echo input_tag('prefijo', $directorio_externo->getPrefijo(), array ('class'=>'form-control input-sm required'));                               
      ?>            
    </div>        
    <!-- Funcionario Destino -->
    <label for="lbfuncionariodestino" class="col-sm-1 control-label">Funcionario Destino:</label>
    <div class="col-sm-6">                    				
      <?php
          echo input_tag('funcionario_destino', $directorio_extended->getFuncionario(), array ('class'=>'form-control input-sm required','onkeyup' => "jQuery('directorioexterno').val() = simad_reset_name(jQuery('directorioexterno').val())",));
      ?>
    </div>          
  </div>
          
  <div class="form-group">
    <!-- Cargo Destino -->
    <label for="lbCargoDestino" class="col-sm-1 control-label">Cargo Destino:</label>
    <div class="col-sm-3">
      <?php
          echo input_tag('cargo_destinatario', $directorio_extended->getCargo(), array ('class'=>'form-control input-sm'));                               
      ?>            
    </div>
    <!-- Funcionario Destino -->
    <label for="lbfuncionariodestino" class="col-sm-1 control-label">Direccion Destino:</label>
    <div class="col-sm-6">                    				
      <?php
          echo input_tag('direccion_destinatario', $directorio_extended->getDireccion(), array ('class'=>'form-control input-sm'));
      ?>
    </div>
  </div>
<?php } ?>