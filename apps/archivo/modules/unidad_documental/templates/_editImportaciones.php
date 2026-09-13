<?php 
use_helper('Object');
?>
<div class="form-group">
  <label for="getNImportacion" class="col-sm-1 control-label">Numero Importación:</label>
  <div class="col-sm-3">
    <?php 
        echo object_input_tag($unidad_documental, 'getNImportacion', array('class' => 'form-control input-sm'));
    ?>
  </div>
  
  <label for="getNDeclaracionImportacion" class="col-sm-1 control-label">Declaración Importación:</label>
  <div class="col-sm-2">
    <?php 
        echo object_input_tag($unidad_documental, 'getNDeclaracionImportacion', array('class' => 'form-control input-sm'));
    ?>
  </div>
  
  <label for="getNLevanteImportacion" class="col-sm-1 control-label">Levante Importacion:</label>
  <div class="col-sm-2">
    <?php 
        echo object_input_tag($unidad_documental, 'getNLevanteImportacion', array('class' => 'form-control input-sm'));
    ?>
  </div>      
</div>
    
<div class="form-group">
  <label for="getNStickerBanco" class="col-sm-1 control-label">Sticker Banco:</label>
  <div class="col-sm-3">
    <?php 
        echo object_input_tag($unidad_documental, 'getNStickerBanco', array('class' => 'form-control input-sm'));
    ?>
  </div>
        
  <label for="getMotonave" class="col-sm-1 control-label">Motonave:</label>
  <div class="col-sm-2">
    <?php 
        echo object_input_tag($unidad_documental, 'getMotonave', array('class' => 'form-control input-sm'));
    ?>
  </div>
  
  <label for="getContenedor" class="col-sm-1 control-label">Prefijo Contenedor:</label>
  <div class="col-sm-2">
    <?php 
        echo object_input_tag($unidad_documental, 'getContenedor', array('class' => 'form-control input-sm'));
    ?>
  </div>                     
</div>
      
<div class="form-group">
  <label for="getNumCompra" class="col-sm-1 control-label">Numero Compra:</label>
  <div class="col-sm-3">
    <?php 
        echo object_input_tag($unidad_documental, 'getNumCompra', array('class' => 'form-control input-sm'));
    ?>
  </div>
  <!-- Fecha Llegada -->
  <label for="fecha_llegada" class="col-sm-1 control-label">Fecha Llegada:</label>
  <div class="col-sm-2">
    <div class="input-group">
      <?php
        echo input_tag('fecha_llegada', '', array('class' => 'form-control input-sm datepicker', 'data-format' => 'yyyy-mm-dd','readonly'=>'readonly'));
      ?>
      <div class="input-group-addon"><a href="#"><i class="entypo-calendar"></i></a></div>
    </div>
  </div>

  <label for="fecha_llegada_final" class="col-sm-1 control-label">Hasta</label>
  <div class="col-sm-2">
    <div class="input-group">
      <?php
        echo input_tag('fecha_llegada_final', '', array('class' => 'form-control input-sm datepicker', 'data-format' => 'yyyy-mm-dd','readonly'=>'readonly'));
      ?>
      <div class="input-group-addon"><a href="#"><i class="entypo-calendar"></i></a></div>
    </div>
  </div>
</div>
    
<div class="form-group">
  <label for="getOrigenImportacion" class="col-sm-1 control-label">Origen Importacion:</label>
  <div class="col-sm-3">
    <?php 
        echo object_input_tag($unidad_documental, 'getOrigenImportacion', array('class' => 'form-control input-sm'));
    ?>
  </div>
</div>