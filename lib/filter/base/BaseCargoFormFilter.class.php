<?php

/**
 * Cargo filter form base class.
 *
 * @package    simad
 * @subpackage filter
 * @author     Your name here
 */
abstract class BaseCargoFormFilter extends BaseFormFilterPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'DESCRIPCION' => new sfWidgetFormFilterInput(),
    ));

    $this->setValidators(array(
      'DESCRIPCION' => new sfValidatorPass(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('cargo_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'Cargo';
  }

  public function getFields()
  {
    return array(
      'CARGO_ID'    => 'Number',
      'DESCRIPCION' => 'Text',
    );
  }
}
