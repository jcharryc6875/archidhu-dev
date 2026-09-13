<?php

/**
 * SolicitudPrestamoEstado filter form base class.
 *
 * @package    simad
 * @subpackage filter
 * @author     Your name here
 */
abstract class BaseSolicitudPrestamoEstadoFormFilter extends BaseFormFilterPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'DESCRIPCION'                => new sfWidgetFormFilterInput(),
    ));

    $this->setValidators(array(
      'DESCRIPCION'                => new sfValidatorPass(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('solicitud_prestamo_estado_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'SolicitudPrestamoEstado';
  }

  public function getFields()
  {
    return array(
      'SOLICITUDPRESTAMOESTADO_ID' => 'Number',
      'DESCRIPCION'                => 'Text',
    );
  }
}
