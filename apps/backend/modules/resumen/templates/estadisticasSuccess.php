<?php
$path_theme = sfConfig::get('theme_simad');
$base_path = sfConfig::get('base_simad');

$currentForm="com_enviada/consulta";
$currentUser= $sf_user->getAttribute('username', '', 'subscriber') ;
$currentUserId= $sf_user->getAttribute('usuario_id','', 'subscriber');

use_helper('Object','jQuery','UserComponent');
?>

<script src="<?php print $path_theme;?>assets/js/chartjs/chart.js"></script>
<script src="<?php print $path_theme;?>assets/js/chartjs/chartjs-plugin-datalabels.min.js"></script>
<script src="<?php print $path_theme;?>assets/js/appsgdea-charts.js?v=<?php echo(rand()); ?>"></script>

<!-- Contenedor Pagina -->
<div class="panel panel-gradient" data-collapsed="0">
  <div class="panel-heading">
    <div class="panel-title"> 
      Consultar Estadisticas
    </div>
  </div>
  <!-- Contenedor Contenido Formulario-->
  <div class="panel-body">      
    <?php echo form_tag('resumen/consultarEstadisticas',array('name'=>'form1','method'=>'GET', 'role' => 'form', 'class' => 'form-horizontal form-groups-bordered validate')) ?>
        <div class="form-group">
          <!-- Marca -->
          <label for="lbMarca" class="col-sm-1 control-label">Seleccione Estadística:</label>
          <div class="col-sm-9">
            <select name="stattype" id="stattype" class="form-control input-sm required">
              <option value="">Seleccione...</option>
              <option value="1">Estadistica comunicaciones recibidas por dependencia</option>
              <option value="2">Estadistica comunicaciones recibidas por tramites</option>   <?php //echo $currentUserId; ?>
              <option value="3">Estadistica comunicaciones recibidas por medio de recepcion</option>     
              <option value="4">Estadistica comunicaciones recibidas con respuesta</option>
              <option value="5">Estadistica comunicaciones enviadas gestionadas</option>         
              <option value="6">Estadistica solicitudes de servicio por tipo</option>         
              <option value="7">Estadistica solicitudes de servicio por estado</option>         
            </select>
          </div> 

        </div>

        <div class="form-group">
          <!-- Tipo de Grafico -->
          <label for="lbMarca" class="col-sm-1 control-label">Seleccione Grafica:</label>
          <div class="col-sm-2">
            <select name="graphtype" id="graphtype" class="form-control input-sm required">
              <option value="0">Seleccione primero la estadistica</option>
              <!-- <option value="1">Barras</option><option value="2">Torta</option><option value="3">Barra Stacked</option><option value="4">Line Chart</option> -->
            </select>
          </div> 
        </div>

        <div class="form-group">          
          <!-- Fecha Creacion -->
            <label for="lbFechacreacion" class="col-sm-1 control-label">Desde:</label>
            <div class="col-sm-2">
                <div class="input-group">
                  <?php
                  echo input_tag('fechaDocInicial', '', array('class' => 'form-control input-sm datepicker required', 'data-format' => 'yyyy-mm-dd','placeholder'=>'AAAA-MM-DD'));
                  ?>
                  <div class="input-group-addon"><a href="#"><i class="entypo-calendar"></i></a></div>
                </div>
            </div>
            <label for="lbFechacreacionfinal" class="col-sm-1 control-label">Hasta:</label>
            <div class="col-sm-2">
                <div class="input-group">
                  <?php
                    echo input_tag('fechaDocFinal', '', array('class' => 'form-control input-sm datepicker required', 'data-format' => 'yyyy-mm-dd','placeholder'=>'AAAA-MM-DD'));
                  ?>
                  <div class="input-group-addon"><a href="#"><i class="entypo-calendar"></i></a></div>
                </div>
            </div>
        </div>
        
        <div class="form-group"> 
        <!-- Botonera -->
        <div class="col-sm-offset-4 col-sm-5">      
            <button type="button" id="searchit" class="btn btn-blue btn-icon">Consultar Registros<i class="entypo-search"></i></button>            
            <?php echo button_to('Deshacer','resumen/estadisticas',array('class' => 'btn btn-red')); ?>
        </div>
    </div>
    <div class="clear"></div>    
    </form>

    <div class="ensayo" id='respuesta'>
    </div>
  </div>
</div>
 
<script type="text/javascript">

//debugger;
var primerSelect = document.getElementById("stattype");
var segundoSelect = document.getElementById("graphtype");

// Opcion 01
var opcion01 = document.createElement('option');
opcion01.value = 1;
opcion01.text = 'Barras';

// Opcion 02
var opcion02 = document.createElement('option');
opcion02.value = 2;
opcion02.text = 'Torta';

// Opcion 03
var opcion03 = document.createElement('option');
opcion03.value = 3;
opcion03.text = 'Barra Stacked';

// Opcion 04
var opcion04 = document.createElement('option');
opcion04.value = 4;
opcion04.text = 'LineChart';

function resetOptions() 
{
  segundoSelect.remove(0);
  segundoSelect.remove(1);
  segundoSelect.remove(2);
  segundoSelect.remove(3);
  segundoSelect.remove(4);
}

primerSelect.addEventListener('change', function() 
{
    resetOptions();
    if (primerSelect.value == 1 || primerSelect.value == 2 || primerSelect.value == 3 || primerSelect.value == 6 || primerSelect.value == 7) 
    {
      resetOptions();
      segundoSelect.appendChild(opcion01);
      segundoSelect.appendChild(opcion02);
    }
    else if (primerSelect.value == 4)
    {
      resetOptions();
      segundoSelect.appendChild(opcion03);
    }
    else if (primerSelect.value == 5)
    {
      resetOptions();
      segundoSelect.appendChild(opcion04);
    }
});
</script> 

<script type="text/javascript">
jQuery(document).ready(function(){
    jQuery('#searchit').on( "click", function(event){
        event.preventDefault();
        var validator = jQuery(this).closest('form').valid();
        if(validator)
        {
            //debugger
            javascript:jQuery.LoadingStructData();   
            jQuery.ajax({
                //debbuger;
                url: '<?php echo url_for('resumen/consultarEstadisticas');?>',
                //url: '<?php //echo url_for('transferencia/unidadPro');?>',
                method: 'POST',
                data: jQuery(this.form.elements).serialize(), //serializar.
                contentType: 'application/x-www-form-urlencoded; charset=UTF-8', 
                cache:false,
                processData: false,
                success: function(response)
                {
                    //jQuery('#<?php //echo md5('strlistinfo');?>').html(response);
                    jQuery('#respuesta').html(response);
                    javascript:jQuery.CloseLoadingStructData();
                    toastr.success("consulta exitosa!!!"); 
                },
                error: function(response)
                {
                    //debugger;
                    //console.log('FALLO!!!!');
                    javascript:jQuery.CloseLoadingStructData();  
                    toastr.error("Error Interno del Servidor!"); 
                }
            });
        }
	});
});
</script>




