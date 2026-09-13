<?php

/**
 * Especialidad filter form base class.
 *
 * @package    simad
 * @subpackage filter
 * @author     Your name here
 */
abstract class BaseEspecialidadFormFilter extends BaseFormFilterPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'DESCRIPCION'     => new sfWidgetFormFilterInput(),
    ));

    $this->setValidators(array(
      'DESCRIPCION'     => new sfValidatorPass(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('especialidad_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'Especialidad';
  }

  public function getFields()
  {
    return array(
      'ESPECIALIDAD_ID' => 'Number',
      'DESCRIPCION'     => 'Text',
    );
  }
}
