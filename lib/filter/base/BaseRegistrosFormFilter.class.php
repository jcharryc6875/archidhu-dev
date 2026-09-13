<?php

/**
 * Registros filter form base class.
 *
 * @package    simad
 * @subpackage filter
 * @author     Your name here
 */
abstract class BaseRegistrosFormFilter extends BaseFormFilterPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'DOCUMENTACION_ID'        => new sfWidgetFormPropelChoice(array('model' => 'Documentacion', 'add_empty' => true)),
      'TIPOESCALA_ID'           => new sfWidgetFormPropelChoice(array('model' => 'TipoEscala', 'add_empty' => true)),
      'CATEGORIAESCALA_ID'      => new sfWidgetFormPropelChoice(array('model' => 'CategoriaEscala', 'add_empty' => true)),
      'ESPECIALIDADREGISTRO_ID' => new sfWidgetFormPropelChoice(array('model' => 'EspecialidadRegistro', 'add_empty' => true)),
      'PAGINACION'              => new sfWidgetFormFilterInput(),
      'MENCION_ESCALA'          => new sfWidgetFormFilterInput(),
      'MENCION_PROYECCION'      => new sfWidgetFormFilterInput(),
      'MENCION_COORDENADAS'     => new sfWidgetFormFilterInput(),
      'TAMANO'                  => new sfWidgetFormFilterInput(),
    ));

    $this->setValidators(array(
      'DOCUMENTACION_ID'        => new sfValidatorPropelChoice(array('required' => false, 'model' => 'Documentacion', 'column' => 'DOCUMENTACION_ID')),
      'TIPOESCALA_ID'           => new sfValidatorPropelChoice(array('required' => false, 'model' => 'TipoEscala', 'column' => 'TIPOESCALA_ID')),
      'CATEGORIAESCALA_ID'      => new sfValidatorPropelChoice(array('required' => false, 'model' => 'CategoriaEscala', 'column' => 'CATEGORIAESCALA_ID')),
      'ESPECIALIDADREGISTRO_ID' => new sfValidatorPropelChoice(array('required' => false, 'model' => 'EspecialidadRegistro', 'column' => 'ESPECIALIDADREGISTRO_ID')),
      'PAGINACION'              => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'MENCION_ESCALA'          => new sfValidatorPass(array('required' => false)),
      'MENCION_PROYECCION'      => new sfValidatorPass(array('required' => false)),
      'MENCION_COORDENADAS'     => new sfValidatorPass(array('required' => false)),
      'TAMANO'                  => new sfValidatorSchemaFilter('text', new sfValidatorNumber(array('required' => false))),
    ));

    $this->widgetSchema->setNameFormat('registros_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'Registros';
  }

  public function getFields()
  {
    return array(
      'REGISTROS_ID'            => 'Number',
      'DOCUMENTACION_ID'        => 'ForeignKey',
      'TIPOESCALA_ID'           => 'ForeignKey',
      'CATEGORIAESCALA_ID'      => 'ForeignKey',
      'ESPECIALIDADREGISTRO_ID' => 'ForeignKey',
      'PAGINACION'              => 'Number',
      'MENCION_ESCALA'          => 'Text',
      'MENCION_PROYECCION'      => 'Text',
      'MENCION_COORDENADAS'     => 'Text',
      'TAMANO'                  => 'Number',
    );
  }
}
