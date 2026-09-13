<?php use_helper('Object','jQuery'); ?>
<tr>
  <th width="95" height="20" class="ColorRight" scope="row" >Creado Por:</th>
  <td colspan="4" class="Color"><?php
   echo select_tag('usuario_id',objects_for_select($UserUniDoc,'getUsuarioId','getNombreAll','',
   array('include_custom'=>'Seleccione Usuario...',)) , array (     
     'name'=>'id_creador',
     'id'=>'id_creador',
	 ));
?></td>
</tr>

<tr>
  <th width="95" height="20" class="ColorRight" scope="row" >Inventariado Por:</th>
  <td colspan="4" class="Color">
<?php    
   echo select_tag('usuario_id',objects_for_select($UserUniDoc,'getUsuarioId','getNombreAll','',
   array('include_custom'=>'Seleccione Usuario...',)) , array (     
     'name'=>'id_inventariador',
     'id'=>'id_inventariador',
	 ));?>
	 </td>
</tr>

<tr>
  <th width="95" height="20" class="ColorRight" scope="row" >Responsable:</th>
  <td colspan="4" class="Color">
  <?php 
  echo input_hidden_tag('form_origen', 'consultar');
  echo select_tag('usuario_id',objects_for_select($UserUniDoc,'getUsuarioId','getNombreAll','',
  array('include_custom'=>'Seleccione Usuario...',)) , array (     
     'name'=>'id_responsable',
     'id'=>'id_responsable',
  ))?>	</td>
</tr>