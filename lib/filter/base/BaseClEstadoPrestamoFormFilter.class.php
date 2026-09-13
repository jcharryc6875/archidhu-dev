<?php

/**
 * ClEstadoPrestamo filter form base class.
 *
 * @package    simad
 * @subpackage filter
 * @author     Your name here
 */
abstract class BaseClEstadoPrestamoFormFilter extends BaseFormFilterPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'DESCRIPCION'         => new sfWidgetFormFilterInput(),
    ));

    $this->setValidators(array(
      'DESCRIPCION'         => new sfValidatorPass(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('cl_estado_prestamo_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'ClEstadoPrestamo';
  }

  public function getFields()
  {
    return array(
      'CLESTADOPRESTAMO_ID' => 'Number',
      'DESCRIPCION'         => 'Text',
    );
  }
}
