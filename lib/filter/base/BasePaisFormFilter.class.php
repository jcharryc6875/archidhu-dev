<?php

/**
 * Pais filter form base class.
 *
 * @package    simad
 * @subpackage filter
 * @author     Your name here
 */
abstract class BasePaisFormFilter extends BaseFormFilterPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'NOMBRE'  => new sfWidgetFormFilterInput(),
    ));

    $this->setValidators(array(
      'NOMBRE'  => new sfValidatorPass(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('pais_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'Pais';
  }

  public function getFields()
  {
    return array(
      'PAIS_ID' => 'Number',
      'NOMBRE'  => 'Text',
    );
  }
}
