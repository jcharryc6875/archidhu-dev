<?php

/**
 * Ciudad filter form base class.
 *
 * @package    simad
 * @subpackage filter
 * @author     Your name here
 */
abstract class BaseCiudadFormFilter extends BaseFormFilterPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'DEPARTAMENTO_ID' => new sfWidgetFormPropelChoice(array('model' => 'Departamento', 'add_empty' => true)),
      'NOMBRE'          => new sfWidgetFormFilterInput(),
    ));

    $this->setValidators(array(
      'DEPARTAMENTO_ID' => new sfValidatorPropelChoice(array('required' => false, 'model' => 'Departamento', 'column' => 'DEPARTAMENTO_ID')),
      'NOMBRE'          => new sfValidatorPass(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('ciudad_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'Ciudad';
  }

  public function getFields()
  {
    return array(
      'CIUDAD_ID'       => 'Number',
      'DEPARTAMENTO_ID' => 'ForeignKey',
      'NOMBRE'          => 'Text',
    );
  }
}
