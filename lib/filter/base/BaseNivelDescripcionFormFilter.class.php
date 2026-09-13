<?php

/**
 * NivelDescripcion filter form base class.
 *
 * @package    simad
 * @subpackage filter
 * @author     Your name here
 */
abstract class BaseNivelDescripcionFormFilter extends BaseFormFilterPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'DESCRIPCION'         => new sfWidgetFormFilterInput(),
    ));

    $this->setValidators(array(
      'DESCRIPCION'         => new sfValidatorPass(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('nivel_descripcion_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'NivelDescripcion';
  }

  public function getFields()
  {
    return array(
      'NIVELDESCRIPCION_ID' => 'Number',
      'DESCRIPCION'         => 'Text',
    );
  }
}
