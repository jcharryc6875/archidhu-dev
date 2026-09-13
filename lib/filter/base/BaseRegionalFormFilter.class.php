<?php

/**
 * Regional filter form base class.
 *
 * @package    simad
 * @subpackage filter
 * @author     Your name here
 */
abstract class BaseRegionalFormFilter extends BaseFormFilterPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'CIUDAD_ID'       => new sfWidgetFormPropelChoice(array('model' => 'Ciudad', 'add_empty' => true)),
      'DESCRIPCION'     => new sfWidgetFormFilterInput(),
      'DIRECCION'       => new sfWidgetFormFilterInput(),
      'ENTIDAD_ID'      => new sfWidgetFormPropelChoice(array('model' => 'Entidad', 'add_empty' => true)),
      'DIRECTORIO_NAME' => new sfWidgetFormFilterInput(),
      'ES_VISIBLE'      => new sfWidgetFormFilterInput(),
      'IMAGE_MEMBRETE'  => new sfWidgetFormFilterInput(),
      'CODIGO'          => new sfWidgetFormFilterInput(),
    ));

    $this->setValidators(array(
      'CIUDAD_ID'       => new sfValidatorPropelChoice(array('required' => false, 'model' => 'Ciudad', 'column' => 'CIUDAD_ID')),
      'DESCRIPCION'     => new sfValidatorPass(array('required' => false)),
      'DIRECCION'       => new sfValidatorPass(array('required' => false)),
      'ENTIDAD_ID'      => new sfValidatorPropelChoice(array('required' => false, 'model' => 'Entidad', 'column' => 'ENTIDAD_ID')),
      'DIRECTORIO_NAME' => new sfValidatorPass(array('required' => false)),
      'ES_VISIBLE'      => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'IMAGE_MEMBRETE'  => new sfValidatorPass(array('required' => false)),
      'CODIGO'          => new sfValidatorPass(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('regional_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'Regional';
  }

  public function getFields()
  {
    return array(
      'REGIONAL_ID'     => 'Number',
      'CIUDAD_ID'       => 'ForeignKey',
      'DESCRIPCION'     => 'Text',
      'DIRECCION'       => 'Text',
      'ENTIDAD_ID'      => 'ForeignKey',
      'DIRECTORIO_NAME' => 'Text',
      'ES_VISIBLE'      => 'Number',
      'IMAGE_MEMBRETE'  => 'Text',
      'CODIGO'          => 'Text',
    );
  }
}
