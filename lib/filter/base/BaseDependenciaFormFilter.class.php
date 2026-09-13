<?php

/**
 * Dependencia filter form base class.
 *
 * @package    simad
 * @subpackage filter
 * @author     Your name here
 */
abstract class BaseDependenciaFormFilter extends BaseFormFilterPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'ENTIDAD_ID'           => new sfWidgetFormPropelChoice(array('model' => 'Entidad', 'add_empty' => true)),
      'OFICINAPRODUCTORA_ID' => new sfWidgetFormPropelChoice(array('model' => 'OficinaProductora', 'add_empty' => true)),
      'NOMBRE'               => new sfWidgetFormFilterInput(),
      'CODIGO'               => new sfWidgetFormFilterInput(),
      'TIEMPO_PRESTAMO'      => new sfWidgetFormFilterInput(),
      'ES_ACTUAL'            => new sfWidgetFormFilterInput(),
      'TIPO_TABLA'           => new sfWidgetFormFilterInput(),
      'SECCION_ID'           => new sfWidgetFormFilterInput(),
      'SUBFONDO_ID'          => new sfWidgetFormFilterInput(),
    ));

    $this->setValidators(array(
      'ENTIDAD_ID'           => new sfValidatorPropelChoice(array('required' => false, 'model' => 'Entidad', 'column' => 'ENTIDAD_ID')),
      'OFICINAPRODUCTORA_ID' => new sfValidatorPropelChoice(array('required' => false, 'model' => 'OficinaProductora', 'column' => 'OFICINAPRODUCTORA_ID')),
      'NOMBRE'               => new sfValidatorPass(array('required' => false)),
      'CODIGO'               => new sfValidatorPass(array('required' => false)),
      'TIEMPO_PRESTAMO'      => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'ES_ACTUAL'            => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'TIPO_TABLA'           => new sfValidatorPass(array('required' => false)),
      'SECCION_ID'           => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'SUBFONDO_ID'          => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
    ));

    $this->widgetSchema->setNameFormat('dependencia_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'Dependencia';
  }

  public function getFields()
  {
    return array(
      'DEPENDENCIA_ID'       => 'Number',
      'ENTIDAD_ID'           => 'ForeignKey',
      'OFICINAPRODUCTORA_ID' => 'ForeignKey',
      'NOMBRE'               => 'Text',
      'CODIGO'               => 'Text',
      'TIEMPO_PRESTAMO'      => 'Number',
      'ES_ACTUAL'            => 'Number',
      'TIPO_TABLA'           => 'Text',
      'SECCION_ID'           => 'Number',
      'SUBFONDO_ID'          => 'Number',
    );
  }
}
