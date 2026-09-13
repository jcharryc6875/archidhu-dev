<?php

/**
 * Isad filter form base class.
 *
 * @package    simad
 * @subpackage filter
 * @author     Your name here
 */
abstract class BaseIsadFormFilter extends BaseFormFilterPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'UNIDADDOCUMENTAL_ID'               => new sfWidgetFormPropelChoice(array('model' => 'UnidadDocumental', 'add_empty' => true)),
      'NIVELDESCRIPCION_ID'               => new sfWidgetFormPropelChoice(array('model' => 'NivelDescripcion', 'add_empty' => true)),
      'CODIGO_REFERENCIA'                 => new sfWidgetFormFilterInput(),
      'TITULO'                            => new sfWidgetFormFilterInput(),
      'FECHA_ACUMULACION'                 => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate())),
      'NOMBRE_PRODUCTOR'                  => new sfWidgetFormFilterInput(),
      'RESENA_BIBLIOGRAFICA'              => new sfWidgetFormFilterInput(),
      'HISTORIA_ARCHIVISTICA'             => new sfWidgetFormFilterInput(),
      'FORMA_INGRESO'                     => new sfWidgetFormFilterInput(),
      'ALCANCE_CONTENIDO'                 => new sfWidgetFormFilterInput(),
      'VALORACION_SELECCION_ELIMINACION'  => new sfWidgetFormFilterInput(),
      'NUEVOS_INGRESOS'                   => new sfWidgetFormFilterInput(),
      'ORGANIZACION'                      => new sfWidgetFormFilterInput(),
      'CONDICIONES_ACCESO'                => new sfWidgetFormFilterInput(),
      'CONDICIONES_REPRODUCCION'          => new sfWidgetFormFilterInput(),
      'LENGUA_ESCRITURA_DOCUMETOS'        => new sfWidgetFormFilterInput(),
      'CARACTERISTICAS_FISICAS'           => new sfWidgetFormFilterInput(),
      'INTRUMENTOS_DESCRIPCION'           => new sfWidgetFormFilterInput(),
      'LOCALIZACION_ORIGINALES'           => new sfWidgetFormFilterInput(),
      'LOCALIZACION_COPIAS'               => new sfWidgetFormFilterInput(),
      'UNIDADES_DESCRIPCION_RELACIONADAS' => new sfWidgetFormFilterInput(),
      'NOTA_DESCRIPCION'                  => new sfWidgetFormFilterInput(),
      'NOTAS'                             => new sfWidgetFormFilterInput(),
      'NOTA_ARCHIVERO'                    => new sfWidgetFormFilterInput(),
      'REGLAS_NORMAS'                     => new sfWidgetFormFilterInput(),
      'FECHA_DESCRIPCIONES'               => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate())),
    ));

    $this->setValidators(array(
      'UNIDADDOCUMENTAL_ID'               => new sfValidatorPropelChoice(array('required' => false, 'model' => 'UnidadDocumental', 'column' => 'UNIDADDOCUMENTAL_ID')),
      'NIVELDESCRIPCION_ID'               => new sfValidatorPropelChoice(array('required' => false, 'model' => 'NivelDescripcion', 'column' => 'NIVELDESCRIPCION_ID')),
      'CODIGO_REFERENCIA'                 => new sfValidatorPass(array('required' => false)),
      'TITULO'                            => new sfValidatorPass(array('required' => false)),
      'FECHA_ACUMULACION'                 => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDate(array('required' => false)), 'to_date' => new sfValidatorDate(array('required' => false)))),
      'NOMBRE_PRODUCTOR'                  => new sfValidatorPass(array('required' => false)),
      'RESENA_BIBLIOGRAFICA'              => new sfValidatorPass(array('required' => false)),
      'HISTORIA_ARCHIVISTICA'             => new sfValidatorPass(array('required' => false)),
      'FORMA_INGRESO'                     => new sfValidatorPass(array('required' => false)),
      'ALCANCE_CONTENIDO'                 => new sfValidatorPass(array('required' => false)),
      'VALORACION_SELECCION_ELIMINACION'  => new sfValidatorPass(array('required' => false)),
      'NUEVOS_INGRESOS'                   => new sfValidatorPass(array('required' => false)),
      'ORGANIZACION'                      => new sfValidatorPass(array('required' => false)),
      'CONDICIONES_ACCESO'                => new sfValidatorPass(array('required' => false)),
      'CONDICIONES_REPRODUCCION'          => new sfValidatorPass(array('required' => false)),
      'LENGUA_ESCRITURA_DOCUMETOS'        => new sfValidatorPass(array('required' => false)),
      'CARACTERISTICAS_FISICAS'           => new sfValidatorPass(array('required' => false)),
      'INTRUMENTOS_DESCRIPCION'           => new sfValidatorPass(array('required' => false)),
      'LOCALIZACION_ORIGINALES'           => new sfValidatorPass(array('required' => false)),
      'LOCALIZACION_COPIAS'               => new sfValidatorPass(array('required' => false)),
      'UNIDADES_DESCRIPCION_RELACIONADAS' => new sfValidatorPass(array('required' => false)),
      'NOTA_DESCRIPCION'                  => new sfValidatorPass(array('required' => false)),
      'NOTAS'                             => new sfValidatorPass(array('required' => false)),
      'NOTA_ARCHIVERO'                    => new sfValidatorPass(array('required' => false)),
      'REGLAS_NORMAS'                     => new sfValidatorPass(array('required' => false)),
      'FECHA_DESCRIPCIONES'               => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDate(array('required' => false)), 'to_date' => new sfValidatorDate(array('required' => false)))),
    ));

    $this->widgetSchema->setNameFormat('isad_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'Isad';
  }

  public function getFields()
  {
    return array(
      'ISAD_ID'                           => 'Number',
      'UNIDADDOCUMENTAL_ID'               => 'ForeignKey',
      'NIVELDESCRIPCION_ID'               => 'ForeignKey',
      'CODIGO_REFERENCIA'                 => 'Text',
      'TITULO'                            => 'Text',
      'FECHA_ACUMULACION'                 => 'Date',
      'NOMBRE_PRODUCTOR'                  => 'Text',
      'RESENA_BIBLIOGRAFICA'              => 'Text',
      'HISTORIA_ARCHIVISTICA'             => 'Text',
      'FORMA_INGRESO'                     => 'Text',
      'ALCANCE_CONTENIDO'                 => 'Text',
      'VALORACION_SELECCION_ELIMINACION'  => 'Text',
      'NUEVOS_INGRESOS'                   => 'Text',
      'ORGANIZACION'                      => 'Text',
      'CONDICIONES_ACCESO'                => 'Text',
      'CONDICIONES_REPRODUCCION'          => 'Text',
      'LENGUA_ESCRITURA_DOCUMETOS'        => 'Text',
      'CARACTERISTICAS_FISICAS'           => 'Text',
      'INTRUMENTOS_DESCRIPCION'           => 'Text',
      'LOCALIZACION_ORIGINALES'           => 'Text',
      'LOCALIZACION_COPIAS'               => 'Text',
      'UNIDADES_DESCRIPCION_RELACIONADAS' => 'Text',
      'NOTA_DESCRIPCION'                  => 'Text',
      'NOTAS'                             => 'Text',
      'NOTA_ARCHIVERO'                    => 'Text',
      'REGLAS_NORMAS'                     => 'Text',
      'FECHA_DESCRIPCIONES'               => 'Date',
    );
  }
}
