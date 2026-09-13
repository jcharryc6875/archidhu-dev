<?php 
use_helper('Object');
?>

<div class="col-sm-12">
  <div class="col-sm-6">
    <div class="form-group">
      <label for="niveldescripcion_id" class="col-sm-3 control-label">Nivel Descripción</label>
      <div class="col-sm-9">
        <?php 
        echo object_select_tag($isad, 'getNiveldescripcionId', array('related_class' => 'NivelDescripcion', 'include_custom'=>'--- Seleccione Nivel Descripcion ---', 'class' => 'form-control input-sm'));
        ?>
      </div>
    </div>
  </div>

  <div class="col-sm-6">
    <div class="form-group">
      <label for="codigo_referencia" class="col-sm-3 control-label">Codigo Referencia</label>
      <div class="col-sm-9">
        <?php 
        echo object_input_tag($isad, 'getCodigoReferencia', array('class' => 'form-control input-sm')); 
        ?>
      </div>
    </div>
  </div>

  <div class="col-sm-6">
    <div class="form-group">
      <label for="titulo_atribuido" class="col-sm-3 control-label">Titulo Atribuido</label>
      <div class="col-sm-9">
        <?php 
        echo input_tag('titulo_atribuido', '', array('class' => 'form-control input-sm')); 
        ?>
      </div>
    </div>
  </div>

  <div class="col-sm-6">
    <div class="form-group">
      <label for="nombre_productor" class="col-sm-3 control-label">Nombre Productor</label>
      <div class="col-sm-9">
        <?php 
        echo object_input_tag($isad, 'getNombreProductor', array('class' => 'form-control input-sm')); 
        ?>
      </div>
    </div>
  </div>

  <div class="col-sm-6">
    <div class="form-group">
      <label for="resena_bibliografica" class="col-sm-3 control-label">Reseña Bibliografica</label>
      <div class="col-sm-9">
        <?php 
        echo object_input_tag($isad, 'getResenaBibliografica', array('class' => 'form-control input-sm')); 
        ?>
      </div>
    </div>
  </div>

  <div class="col-sm-6">
    <div class="form-group">
      <label for="historia_archivistica" class="col-sm-3 control-label">Historia Archivistica</label>
      <div class="col-sm-9">
        <?php 
        echo object_input_tag($isad, 'getHistoriaArchivistica', array('class' => 'form-control input-sm')); 
        ?>
      </div>
    </div>
  </div>

  <div class="col-sm-6">
    <div class="form-group">
      <label for="forma_ingreso" class="col-sm-3 control-label">Forma Ingreso</label>
      <div class="col-sm-9">
        <?php 
        echo object_input_tag($isad, 'getFormaIngreso', array('class' => 'form-control input-sm')); 
        ?>
      </div>
    </div>
  </div>

  <div class="col-sm-6">
    <div class="form-group">
      <label for="alcance_contenido" class="col-sm-3 control-label">Alcance Contenido</label>
      <div class="col-sm-9">
        <?php 
        echo object_input_tag($isad, 'getAlcanceContenido', array('class' => 'form-control input-sm')); 
        ?>
      </div>
    </div>
  </div>

  <div class="col-sm-6">
    <div class="form-group">
      <label for="valoracion_seleccion_eliminacion" class="col-sm-3 control-label">Valoración, Selección y Eliminación</label>
      <div class="col-sm-9">
        <?php 
        echo object_input_tag($isad, 'getValoracionSeleccionEliminacion', array('class' => 'form-control input-sm')); 
        ?>
      </div>
    </div>
  </div>

  <div class="col-sm-6">
    <div class="form-group">
      <label for="nuevos_ingresos" class="col-sm-3 control-label">Nuevos Ingresos</label>
      <div class="col-sm-9">
        <?php 
        echo object_input_tag($isad, 'getNuevosIngresos', array('class' => 'form-control input-sm')); 
        ?>
      </div>
    </div>
  </div>

  <div class="col-sm-6">
    <div class="form-group">
      <label for="organizacion" class="col-sm-3 control-label">Organización</label>
      <div class="col-sm-9">
        <?php 
        echo object_input_tag($isad, 'getOrganizacion', array('class' => 'form-control input-sm')); 
        ?>
      </div>
    </div>
  </div>

  <div class="col-sm-6">
    <div class="form-group">
      <label for="condiciones_acceso" class="col-sm-3 control-label">Condiciones de Acceso</label>
      <div class="col-sm-9">
        <?php 
        echo object_input_tag($isad, 'getCondicionesAcceso', array('class' => 'form-control input-sm')); 
        ?>
      </div>
    </div>
  </div>

  <div class="col-sm-6">
    <div class="form-group">
      <label for="condiciones_reproduccion" class="col-sm-3 control-label">Condiciones de Reproducción</label>
      <div class="col-sm-9">
        <?php 
        echo object_input_tag($isad, 'getCondicionesReproduccion', array('class' => 'form-control input-sm')); 
        ?>
      </div>
    </div>
  </div>

  <div class="col-sm-6">
    <div class="form-group">
      <label for="lengua_escritura_documetos" class="col-sm-3 control-label">Lengua, Escritura Documentos</label>
      <div class="col-sm-9">
        <?php 
        echo object_input_tag($isad, 'getLenguaEscrituraDocumetos', array('class' => 'form-control input-sm')); 
        ?>
      </div>
    </div>
  </div>

  <div class="col-sm-6">
    <div class="form-group">
      <label for="caracteristicas_fisicas" class="col-sm-3 control-label">Caracteristicas Fisicas</label>
      <div class="col-sm-9">
        <?php 
        echo object_input_tag($isad, 'getCaracteristicasFisicas', array('class' => 'form-control input-sm')); 
        ?>
      </div>
    </div>
  </div>

  <div class="col-sm-6">
    <div class="form-group">
      <label for="intrumentos_descripcion" class="col-sm-3 control-label">Instrumentos Descripción</label>
      <div class="col-sm-9">
        <?php 
        echo object_input_tag($isad, 'getIntrumentosDescripcion', array('class' => 'form-control input-sm')); 
        ?>
      </div>
    </div>
  </div>

  <div class="col-sm-6">
    <div class="form-group">
      <label for="localizacion_originales" class="col-sm-3 control-label">Localización Originales</label>
      <div class="col-sm-9">
        <?php 
        echo object_input_tag($isad, 'getLocalizacionOriginales', array('class' => 'form-control input-sm')); 
        ?>
      </div>
    </div>
  </div>

  <div class="col-sm-6">
    <div class="form-group">
      <label for="localizacion_copias" class="col-sm-3 control-label">Localización Copias</label>
      <div class="col-sm-9">
        <?php 
        echo object_input_tag($isad, 'getLocalizacionCopias', array('class' => 'form-control input-sm')); 
        ?>
      </div>
    </div>
  </div>

  <div class="col-sm-6">
    <div class="form-group">
      <label for="unidades_descripcion_relacionadas" class="col-sm-3 control-label">Descripción Relacionadas</label>
      <div class="col-sm-9">
        <?php 
        echo object_input_tag($isad, 'getUnidadesDescripcionRelacionadas', array('class' => 'form-control input-sm')); 
        ?>
      </div>
    </div>
  </div>

  <div class="col-sm-6">
    <div class="form-group">
      <label for="nota_descripcion" class="col-sm-3 control-label">Nota Descrpción</label>
      <div class="col-sm-9">
        <?php 
        echo object_input_tag($isad, 'getNotaDescripcion', array('class' => 'form-control input-sm')); 
        ?>
      </div>
    </div>
  </div>

  <div class="col-sm-6">
    <div class="form-group">
      <label for="notas" class="col-sm-3 control-label">Notas</label>
      <div class="col-sm-9">
        <?php 
        echo object_input_tag($isad, 'getNotas', array('class' => 'form-control input-sm')); 
        ?>
      </div>
    </div>
  </div>

  <div class="col-sm-6">
    <div class="form-group">
      <label for="nota_archivero" class="col-sm-3 control-label">Nota Archivero</label>
      <div class="col-sm-9">
        <?php 
        echo object_input_tag($isad, 'getNotaArchivero', array('class' => 'form-control input-sm')); 
        ?>
      </div>
    </div>
  </div>

  <div class="col-sm-6">
    <div class="form-group">
      <label for="reglas_normas" class="col-sm-3 control-label">Regla o Normas</label>
      <div class="col-sm-9">
        <?php 
        echo object_input_tag($isad, 'getReglasNormas', array('class' => 'form-control input-sm')); 
        ?>
      </div>
    </div>
  </div>

  <div class="col-sm-6">
    <div class="form-group">
      <label for="fecha_acumulacion" class="col-sm-3 control-label">Fecha Acumulación</label>
      <div class="col-sm-9">
        <div class="input-group">
          <?php 
          echo object_input_tag($isad, 'getFechaAcumulacion', array('class' => 'form-control input-sm datepicker', 'data-format' => 'yyyy-mm-dd','readonly'=>'readonly')); 
          ?>
          <div class="input-group-addon"><a href="#"><i class="entypo-calendar"></i></a></div>
        </div>
      </div>
    </div>
  </div>

  <div class="col-sm-6">
    <div class="form-group">
      <label for="fecha_descripciones" class="col-sm-3 control-label">Fecha Descripciones</label>
      <div class="col-sm-9">
        <div class="input-group">
          <?php 
          echo object_input_tag($isad, 'getFechaDescripciones', array('class' => 'form-control input-sm datepicker', 'data-format' => 'yyyy-mm-dd','readonly'=>'readonly')); 
          ?>
          <div class="input-group-addon"><a href="#"><i class="entypo-calendar"></i></a></div>
        </div>
      </div>
    </div>
  </div>
</div>