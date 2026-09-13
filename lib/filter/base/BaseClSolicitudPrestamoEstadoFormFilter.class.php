<?php

/**
 * ClSolicitudPrestamoEstado filter form base class.
 *
 * @package    simad
 * @subpackage filter
 * @author     Your name here
 */
abstract class BaseClSolicitudPrestamoEstadoFormFilter extends BaseFormFilterPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'DESCRIPCION'                  => new sfWidgetFormFilterInput(),
    ));

    $this->setValidators(array(
      'DESCRIPCION'                  => new sfValidatorPass(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('cl_solicitud_prestamo_estado_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'ClSolicitudPrestamoEstado';
  }

  public function getFields()
  {
    return array(
      'CLSOLICITUDPRESTAMOESTADO_ID' => 'Number',
      'DESCRIPCION'                  => 'Text',
    );
  }
}
