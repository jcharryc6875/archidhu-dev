<?php 
$path_theme = sfConfig::get('theme_simad');
$base_path = sfConfig::get('base_simad');
use_helper('Object','jQuery');
?>
<h1>Work Flow</h1>

<table  cellspacing="0" width="98%" align=center>
<thead>
<caption>
Flujo 
  </caption>
<tr class="encabezado_tabla">
  <th scope="col" class="nobg">Flujo ID</th>
  <th scope="col" class="nobg">Detalles</th>

  <th scope="col" class="nobg">Tipo Recibida</th>
  <th scope="col" class="nobg">Tipo Interna</th>
  <th scope="col" class="nobg">Buzon</th>
  <th scope="col" class="nobg">Descripcion</th>
</tr>
</thead>
<tbody>
<?php foreach ($wf_flujos as $wf_flujo): ?>
<tr>
    <td><?php echo link_to($wf_flujo->getWfFlujoId(), 'wf_flujo/show?wf_flujo_id='.$wf_flujo->getWfFlujoId()) ?></td>
    <td><?php echo link_to($wf_flujo->getWfFlujoId(), 'wf_flujo/show?wf_flujo_id='.$wf_flujo->getWfFlujoId()) ?></td>

      <td><?php echo $wf_flujo->getTipocomrecibidaId() ?></td>
      <td><?php echo $wf_flujo->getTipocominternaId() ?></td>
      <td><?php echo $wf_flujo->getWfBuzonId() ?></td>
      <td><?php echo $wf_flujo->getDescripcion() ?></td>
  </tr>
<?php endforeach; ?>
</tbody>
</table>

<table width="99%"  align=center border="0" cellpadding="0" cellspacing="0">
  <tr>

<td  class="BotoneraBlanco">
<a href="#" onclick="javascript:openVentana('<?php echo $base_path; ?>/administracion.php/wf_flujo/create'); return false;">
<img border=0 src="<?php echo $base_path; ?>/images/simad/ico_crear_nuevo.png" alt="Crear" width="40" height="40" align="middle" />Crear</a>
</td>  



</td>
</tr>
</table>

