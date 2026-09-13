<div class="col-sm-6">
	<div class="form-group">
		<!-- actividad_inicial -->
		<label for="lbactividad_inicial" class="control-label">Actividad Inicial:</label>
		<select name="actividad_inicial" id="actividad_inicial" class="form-control input-sm required">
            <option value="0">Seleccione...</option>
            <?php                     
            foreach($wf_permisos_transicion_activida as $t)
            {
                echo "<option value='".$t->getWfActividad()->getWfActividadId()."'";
                if($t->getWfActividadId()== $actividad_inicial_original){
                    echo " selected ";
                }
                echo ">".$t->getWfActividad()->getDescripcion()."</option>";
            }
            ?>
        </select>
	</div>
</div>
<div class="col-sm-6">
	<div class="form-group">
		<!-- actividad_final -->
		<label for="lbactividad_final" class="control-label">Actividad Final:</label>
		<select name="actividad_final" id="actividad_final" class="form-control input-sm required">
            <option value="0">Seleccione...</option>
            <?php                     
            foreach($wf_permisos_transicion_activida as $t)
            {
                echo "<option value='".$t->getWfActividad()->getWfActividadId()."'";
                if($t->getWfActividadId()== $actividad_final_original){
                    echo " selected ";
                }
                echo ">".$t->getWfActividad()->getDescripcion()."</option>";
            }
            ?>
        </select>
	</div>
</div>