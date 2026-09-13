<?php

/**
 * PrioridadPqr filter form base class.
 *
 * @package    simad
 * @subpackage filter
 * @author     Your name here
 */
abstract class BasePrioridadPqrFormFilter extends BaseFormFilterPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'DESCRIPCION'     => new sfWidgetFormFilterInput(),
    ));

    $this->setValidators(array(
      'DESCRIPCION'     => new sfValidatorPass(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('prioridad_pqr_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'PrioridadPqr';
  }

  public function getFields()
  {
    return array(
      'PRIORIDADPQR_ID' => 'Number',
      'DESCRIPCION'     => 'Text',
    );
  }
}
