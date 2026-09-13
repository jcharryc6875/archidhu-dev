<?php use_helper('Object') ?>
<?php if($directorio_externo != null) { ?>
<div class="form-group">
  <!-- Prefijo -->
  <label for="lbprefijo" class="col-sm-1 control-label">Prefijo:</label>
  <div class="col-sm-3">
    <?php
        echo input_tag('prefijo', trim($directorio_externo->getPrefijo()), array ('class'=>'form-control input-sm required'));                               
    ?>            
  </div>        
  <!-- Funcionario Destino -->
  <label for="lbfuncionariodestino" class="col-sm-1 control-label">Funcionario Destino:</label>
  <div class="col-sm-6">                    				
        <?php
            echo input_tag('funcionario_destino', trim($directorio_externo->getFuncionario()), array ('class'=>'form-control input-sm required','onkeyup' => "jQuery('directorioexterno').val() = simad_reset_name(jQuery('directorioexterno').val())",));
        ?>
  </div>          
</div>
        
<div class="form-group">
  <!-- Cargo Destino -->
  <label for="lbCargoDestino" class="col-sm-1 control-label">Cargo Destino:</label>
  <div class="col-sm-3">
    <?php
        echo input_tag('cargo_destinatario', trim($directorio_externo->getCargo()), array ('class'=>'form-control input-sm'));                               
    ?>            
  </div>          
  <!-- Funcionario Destino -->
  <label for="lbfuncionariodestino" class="col-sm-1 control-label">Direccion Destino:</label>
  <div class="col-sm-6">                    				
        <?php
            echo input_tag('direccion_destinatario', trim($directorio_externo->getDireccion()), array ('class'=>'form-control input-sm'));
        ?>
  </div>          
</div>
<?php }else{ ?>
<div class="form-group">
  <!-- Prefijo -->
  <label for="lbprefijo" class="col-sm-1 control-label">Prefijo:</label>
  <div class="col-sm-3">
    <?php
        echo input_tag('prefijo', '', array ('class'=>'form-control input-sm required'));                               
    ?>            
  </div>        
  <!-- Funcionario Destino -->
  <label for="lbfuncionariodestino" class="col-sm-1 control-label">Funcionario Destino:</label>
  <div class="col-sm-6">                    				
        <?php
            echo input_tag('funcionario_destino', '', array ('class'=>'form-control input-sm required','onkeyup' => "jQuery('directorioexterno').val() = simad_reset_name(jQuery('directorioexterno').val())",));
        ?>
  </div>          
</div>
        
<div class="form-group">
  <!-- Cargo Destino -->
  <label for="lbCargoDestino" class="col-sm-1 control-label">Cargo Destino:</label>
  <div class="col-sm-3">
    <?php
        echo input_tag('cargo_destinatario', '', array ('class'=>'form-control input-sm'));                               
    ?>            
  </div>          
  <!-- Funcionario Destino -->
  <label for="lbfuncionariodestino" class="col-sm-1 control-label">Direccion Destino:</label>
  <div class="col-sm-6">                    				
        <?php
            echo input_tag('direccion_destinatario', '', array ('class'=>'form-control input-sm'));
        ?>
  </div>          
</div>
<?php } ?>