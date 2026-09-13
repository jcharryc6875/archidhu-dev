
    <div class="panel panel-primary">
        <div class="panel-heading">
            <div class="panel-title">Mis ultimos radicados <?php echo $periodo_id; ?></div>
        </div>

         
            <table class="table table-bordered table-responsive">
                <thead>
                    <tr>
                        <th>Radicado</th>
                        <th>Tramite</th>
                        <th>Asunto</th>
                        <th>Fecha Radicación</th>
                    </tr>
                </thead>

                
                <tbody id="misRadicados">
                    <?php
                    foreach ($objects_list as $row) 
                    {
                    ?>
                    <tr>
                      <td><?php  echo $row['RADICADO']; ?></td>
                      <td><?php  echo $row['DESCRIPCION']; ?></td>
                      <td><?php  echo $row['ASUNTO']; ?></td>
                      <td><?php  echo $row['FECHA_CREACION']; ?></td>
                    </tr>

                    <?php
                    }
                    ?>
                </tbody> 


            </table>
        
    </div>


<script type="text/javascript">

function listarUltimosRadicados() 
  {
    jQuery.ajax(
    {
      url: 'resumen/UltimosRadicadosTabla',
      data: jQuery.param({ periodo_id: <?php echo $periodo_id; ?> }),
      type: 'POST',
      success: function(response) 
      {
        //const losRadicados = JSON.parse(response);
        //const losRadicados = jQuery.parseJSON(response); //<<-- resuelto por mi con STACK OVERFLOW


        //console.log(response);
        jQuery('.ensayo').html(response);

        /* 
        const losRadicados = response;
        console.log(losRadicados)
        
        debugger;
        
        let contenido = '';

        losRadicados.forEach(elRadicado => 
        {
            contenido += `
                  <tr>
                      <td>${elRadicado.RADICADO}</td>
                      <td>${elRadicado.DESCRIPCION}</td>
                      <td>${elRadicado.ASUNTO}</td>
                      <td>${elRadicado.FECHA_CREACION}</td>
                  </tr>`
        });

        jQuery('#misRadicados').html(contenido);

        */
        
      }
    });
  }


  listarUltimosRadicados(); 


</script>