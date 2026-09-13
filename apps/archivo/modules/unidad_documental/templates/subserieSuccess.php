<?php 
$path_theme = sfConfig::get('theme_simad');
$base_path = sfConfig::get('base_simad');

use_helper('Object');
use_helper('jQuery');
?>
<div class="row">
<?php
echo form_tag('unidad_documental/list', array('name'=>'consultar','method'=>'GET'));
echo input_hidden_tag('permiso', $sf_params->get('permiso'));
echo input_hidden_tag('form_tag', $sf_params->get('form_tag'));
?>
  <div class="col-sm-12">
    <!-- Contenedor Pagina -->
  <div class="panel panel-gradient" data-collapsed="0">

    <div class="panel-heading">
      <div class="panel-title">
        Seleccionar Subserie
      </div>
    </div>

        <!-- Contenedor Contenido Formulario-->
    <div class="panel-body">

      <?php
      // Control Manejo Combos
      if($tipo_manejo_trd == 2){
      ?>
      <!-- Macroproceso -->
      <div class="form-group">
        <label class="control-label col-sm-3">Macro Proceso</label>
        <div class="col-sm-9">
          <select id="macroproceso" name="macroproceso" class="form-control input-sm">
            <option value="0" selected="selected">Seleccione...</option>
            <?php 
            $macro_procesos = $listado_elementos;
            foreach($macro_procesos as $macro_proceso){          
              echo "<option value='".$macro_proceso->getMacroprocesoId()."'";
              if($macro_proceso->getMacroprocesoId() == $elemento_seleccionado){
                echo " selected ";
              }
              echo ">".ucwords(mb_strtolower($macro_proceso->getDescripcion()))."</option>";
            }
            ?>
          </select>
        </div>
      </div>

      <!-- Proceso -->
      <div class="form-group">
        <label class="control-label col-sm-3">Proceso</label>
        <div class="col-sm-9">
          <select id="proceso" name="proceso" disabled="true" class="form-control input-sm">
            <option value="0" selected="selected">Seleccione Macro Proceso...</option>
          </select>
        </div>
      </div>

      <!-- Oficina Productora -->
      <div class="form-group">
        <label class="control-label col-sm-3">Oficina Productora</label>
        <div class="col-sm-9">
          <select id="unidad_administrativa" name="unidad_administrativa" disabled="true" class="form-control input-sm">
            <option value="0" selected="selected">Seleccione Proceso...</option>
          </select>
        </div>
      </div>

      <!-- Dependencia Nueva -->
      <div class="form-group">
        <label class="control-label col-sm-3">Unidad Administrativa Nueva</label>
        <div class="col-sm-9">
          <select name="dependencias" id="dependencias" disabled="true" class="form-control input-sm">
            <option value="0">Seleccione Oficina Productora...</option>
          </select>
        </div>
      </div>

      <?php
      }else{
      ?>

      <!-- Dependencia Nueva -->
      <div class="form-group">
        <label class="control-label col-sm-3">Unidad Administrativa Nueva</label>
        <div class="col-sm-9">
          <select name="dependencias" id="dependencias" class="form-control input-sm">
            <option value="0">Seleccione Unidad Administrativa...</option>
            <?php
            $dependencias = $listado_elementos;
            $dependencia_compuesto = "";
            foreach($dependencias as $dependencia){
              $dependencia_nombre = ($dependencia->getNombre());
              $dependencia_compuesto = $dependencia_nombre." - ".$dependencia->getCodigo()." - ".$dependencia->getTipoTabla()." - ".$dependencia->getEntidad();
              echo "<option value='".$dependencia->getDependenciaId()."'";
              if($dependencia->getDependenciaId() == $elemento_seleccionado){
                echo " selected ";
              }       
              echo ">".$dependencia_compuesto."</option>";
            }
            ?>    
        </select>
        </div>
      </div>
      <?php
      }
      ?>

      <?php 
      // Si Carga desde formulario consulta
      if($form_tag=="consulta"){
      ?>
      <!-- Dependencia Anterior -->
      <div class="form-group">
        <label class="control-label col-sm-3">Unidad Administrativa Anterior</label>
        <div class="col-sm-9">
          <div id="contenedor_dependencias_anterior">
            <select name="dependencias_anterior" id="dependencias_anterior" disabled="true" class="form-control input-sm">
              <option value="0">Seleccione Elemento...</option>
            </select>
          </div>
        </div>
      </div>
      <?php
      }
      ?>

      <!-- Serie -->
      <div class="form-group">
        <label class="control-label col-sm-3">Serie</label>
        <div class="col-sm-9">
          <select id="series" name="series" disabled="true" class="form-control input-sm">
            <option value="0" selected="selected">Seleccione Unidad Administrativa</option>
          </select>
        </div>
      </div>

      <!-- Subserie -->
      <div class="form-group">
        <label class="control-label col-sm-3">Subserie</label>
        <div class="col-sm-9">
          <select id="subseries" name="subseries" disabled="true" class="form-control input-sm">
            <option value="0" selected="selected">Seleccione Serie</option>
          </select>
        </div>
      </div>

      <div class="form-group row">
        <!-- Botonera -->
        <div class="col-sm-offset-2 col-sm-5">
          <a href="#" onclick="javascript:jQuery.Seleccionar();" class="btn btn-white btn-sm"><?php echo image_tag('simad/ico_uncheck.png', array('border'=>"0",'width'=>"25",'align'=>"middle",'title'=>'Seleccionar Subserie')).'Seleccionar';?></a>
        </div>
      </div>
      <div class="clear"></div>

    </div>
  </div>
  </div>
</form>
</div>

<!-- Para Pasar a Archivo JS - SIMAD Archivo -->
<script type="text/javascript">
jQuery(document).ready(function($) {
  jQuery.Seleccionar = function() {
      id_subserie = jQuery('#subseries').val();
      
      if(id_subserie != 0){
        parent.jQuery('#subserie_id').val(id_subserie);
        parent.jQuery('#serie_documental').val(jQuery("#subseries option:selected").text());
        parent.jQuery.CloseModalSIMAD();
      }else{
         alert("Debe Seleccionar Una Subserie!!");
      }       
  }
});
</script>

<script type="text/javascript">
jQuery(document).ready(function($) {
  jQuery("select").change(function(){
        // Vector para saber cu�l es el siguiente combo a llenar
        var combos = new Array();        
        combos['macroproceso'] = "proceso";
        combos['proceso'] = "unidad_administrativa";
        combos['unidad_administrativa'] = "dependencias";
        combos['dependencias'] = "series";
        combos['dependencias_anterior'] = "series";
        combos['series'] = "subseries";
        // Tomo el nombre del combo al que se le a dado el clic por ejemplo: dependencia
        posicion = jQuery(this).attr("name");
        //adiciono al array los combos a deshabilitar
        switch(posicion)
        {
          case 'macroproceso':
          jQuery("#proceso").html('<option value="0" selected="selected">Seleccione Macro Proceso...</option>')
          jQuery("#unidad_administrativa").html('<option value="0" selected="selected">Seleccione Proceso...</option>')
          jQuery("#dependencias").html('<option value="0" selected="selected">Seleccione Oficina Productora...</option>')
          jQuery("#series").html('<option value="0" selected="selected">Seleccione Unidad Administrativa...</option>')
          jQuery("#subseries").html('<option value="0" selected="selected">Seleccione Subserie...</option>')
            //deshabilita los combos siguientes
            jQuery("#proceso").attr('disabled',true);
            jQuery("#unidad_administrativa").attr('disabled',true);
            jQuery("#dependencias").attr('disabled',true);
            jQuery("#dependencias_anterior").attr('disabled',true);
            jQuery("#series").attr('disabled',true);
            jQuery("#subseries").attr('disabled',true);
            // Tomo el valor de la opci�n seleccionada
            valor = jQuery(this).val(); 
            break;
            case 'proceso':            
            jQuery("#unidad_administrativa").html('<option value="0" selected="selected">Seleccione Proceso...</option>')
            jQuery("#dependencias").html('<option value="0" selected="selected">Seleccione Oficina Productora...</option>')
            jQuery("#series").html('<option value="0" selected="selected">Seleccione Unidad Administrativa...</option>')
            jQuery("#subseries").html('<option value="0" selected="selected">Seleccione Subserie...</option>')
            //deshabilita los combos siguientes            
            jQuery("#unidad_administrativa").attr('disabled',true);
            jQuery("#dependencias").attr('disabled',true);
            jQuery("#dependencias_anterior").attr('disabled',true);
            jQuery("#series").attr('disabled',true);
            jQuery("#subseries").attr('disabled',true);
            // Tomo el valor de la opci�n seleccionada
            valor = jQuery(this).val();
            break;
            case 'unidad_administrativa':            
            jQuery("#dependencias").html('<option value="0" selected="selected">Seleccione Oficina Productora...</option>')
            jQuery("#series").html('<option value="0" selected="selected">Seleccione Unidad Administrativa...</option>')
            jQuery("#subseries").html('<option value="0" selected="selected">Seleccione Subserie...</option>')
            //deshabilita los combos siguientes
            jQuery("#dependencias").attr('disabled',true);
            jQuery("#dependencias_anterior").attr('disabled',true);
            jQuery("#series").attr('disabled',true);
            jQuery("#subseries").attr('disabled',true);
            // Tomo el valor de la opci�n seleccionada
            valor = jQuery(this).val();
            break;
            case 'dependencias':
            jQuery("#series").html('<option value="0" selected="selected">Seleccione Unidad Administrativa...</option>')
            jQuery("#subseries").html('<option value="0" selected="selected">Seleccione Subserie...</option>')            
            //deshabilita los combos siguientes            
            jQuery("#series").attr('disabled',true);
            jQuery("#subseries").attr('disabled',true);                        
            // Tomo el valor de la opci�n seleccionada
            valor = jQuery(this).val();
            if(valor == "0"){
              jQuery("#dependencias_anterior").attr('disabled',false);
            }else{
              jQuery("#dependencias_anterior").attr('disabled',true);
              jQuery("#dependencias_anterior").val("0");
            }
            break;
            case 'dependencias_anterior':                        
            jQuery("#series").html('<option value="0" selected="selected">Seleccione Unidad Administrativa...</option>')
            jQuery("#subseries").html('<option value="0" selected="selected">Seleccione Subserie...</option>')
            //deshabilita los combos siguientes            
            jQuery("#series").attr('disabled',true);
            jQuery("#subseries").attr('disabled',true);
            // Tomo el valor de la opci�n seleccionada
            valor = jQuery(this).val();
            if(valor == "0"){
              jQuery("#dependencias").attr('disabled',false);
            }else{
              jQuery("#dependencias").attr('disabled',true);
              jQuery("#dependencias").val("0");
            }
            break;
            case 'series':            
            jQuery("#subseries").html('<option value="0" selected="selected">Seleccione Subserie...</option>')
            //deshabilita los combos siguientes            
            jQuery("#subseries").attr('disabled',true);
            // Tomo el valor de la opci�n seleccionada
            valor = jQuery(this).val();
            break;
            //default: caption ="default";
          }        
        // Evalu�  si es el primer combo y el valor es 0, vaci� los demas combos
        if(valor != 0){        
        /* En caso contrario agregado el letrero de cargando a el combo siguiente
        Ejemplo: Si seleccione dependencia voy a tener que el siguiente combo seg�n mi vector combos
        */
        jQuery("#"+combos[posicion]).html('<option selected="selected" value="0">Cargando Elementos...</option>')
        /* Verificamos si el valor seleccionado es diferente de 0 y si el combo es diferente de subseries, esto porque no tendr�a caso hacer la consulta a subseries porque no existe un combo dependiente de este */
        if(valor!="0" || posicion !="subseries"){
          if(jQuery(this).attr("name") == "dependencias_anterior"){
            combo_selected = "dependencias";
          }else{
            combo_selected = jQuery(this).attr("name");
          }
            // Llamamos laa accion de combos.php donde ejecuto las consultas para llenar los combos               
            jQuery.post("cargarCombos",{
                                    combo_seleccionado:combo_selected, // Nombre del combo
                                    id_seleccionado:jQuery(this).val(), // Valor seleccionado
                                    id_permiso:jQuery("#permiso").val(), // Valor del permiso
                                    form_tag:jQuery("#form_tag").val(), // Valor origen del formulario
                                    addcomp: "1"
                                  },function(data){
                                                    jQuery("#"+combos[posicion]).html(data);//Tomo el resultado de pagina e inserto los datos en el combo indicado                                                    
                                                    jQuery("#"+combos[posicion]).attr('disabled',false);//habilita el siguiente combo
                                                  })
            if(posicion == "unidad_administrativa"){
              var combo_seleccionado_name = "dependencias_anterior";                        
              jQuery.post("cargarCombos",{
                            combo_seleccionado:combo_seleccionado_name, // Nombre del combo
                            id_seleccionado:jQuery(this).val(), // Valor seleccionado
                            id_permiso:jQuery("#permiso").val(), // Valor del permiso
                            form_tag:jQuery("#form_tag").val(), // Valor origen del formulario
                            addcomp: "1"
                          },function(data){
                                jQuery("#"+combo_seleccionado_name).html(data);//Tomo el resultado de pagina e inserto los datos en el combo indicado                                                    
                                jQuery("#"+combo_seleccionado_name).attr('disabled',false);//habilita el siguiente combo
                              })
            }
          }
        }
      })
})
</script>