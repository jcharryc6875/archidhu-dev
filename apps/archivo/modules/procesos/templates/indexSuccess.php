<h1>Procesos List</h1>

<table>
  <thead>
    <tr>
      <th>Procesos</th>
      <th>Macroproceso</th>
      <th>Descripcion</th>
      <th>Codigo</th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($procesosList as $procesos): ?>
    <tr>
      <td><a href="<?php echo url_for('procesos/edit?procesos_id='.$procesos->getProcesosId()) ?>"><?php echo $procesos->getProcesosId() ?></a></td>
      <td><?php echo $procesos->getMacroprocesoId() ?></td>
      <td><?php echo $procesos->getDescripcion() ?></td>
      <td><?php echo $procesos->getCodigo() ?></td>
    </tr>
    <?php endforeach; ?>
  </tbody>
</table>

<a href="<?php echo url_for('procesos/create') ?>">Create</a>
