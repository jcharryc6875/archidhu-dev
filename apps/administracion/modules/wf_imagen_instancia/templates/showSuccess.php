<table>
  <tbody>
    <tr>
      <th>Wf imagen instancia:</th>
      <td><?php echo $wf_imagen_instancia->getWfImagenInstanciaId() ?></td>
    </tr>
    <tr>
      <th>Wfinstancia:</th>
      <td><?php echo $wf_imagen_instancia->getWfinstanciaId() ?></td>
    </tr>
    <tr>
      <th>Wf estado imagen:</th>
      <td><?php echo $wf_imagen_instancia->getWfEstadoImagenId() ?></td>
    </tr>
    <tr>
      <th>Descripcion:</th>
      <td><?php echo $wf_imagen_instancia->getDescripcion() ?></td>
    </tr>
    <tr>
      <th>Ruta:</th>
      <td><?php echo $wf_imagen_instancia->getRuta() ?></td>
    </tr>
    <tr>
      <th>Fecha creacion:</th>
      <td><?php echo $wf_imagen_instancia->getFechaCreacion() ?></td>
    </tr>
    <tr>
      <th>Folios:</th>
      <td><?php echo $wf_imagen_instancia->getFolios() ?></td>
    </tr>
    <tr>
      <th>Fecha documento:</th>
      <td><?php echo $wf_imagen_instancia->getFechaDocumento() ?></td>
    </tr>
  </tbody>
</table>

<hr />

<a href="<?php echo url_for('wf_imagen_instancia/edit?wf_imagen_instancia_id='.$wf_imagen_instancia->getWfImagenInstanciaId()) ?>">Edit</a>
&nbsp;
<a href="<?php echo url_for('wf_imagen_instancia/index') ?>">List</a>
