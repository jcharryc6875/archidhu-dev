<?php

/**
 * ClSolicitudPrestamo filter form base class.
 *
 * @package    simad
 * @subpackage filter
 * @author     Your name here
 */
abstract class BaseClSolicitudPrestamoFormFilter extends BaseFormFilterPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'CLIENTE_ID'                   => new sfWidgetFormPropelChoice(array('model' => 'Cliente', 'add_empty' => true)),
      'USUARIO_ID'                   => new sfWidgetFormPropelChoice(array('model' => 'Usuario', 'add_empty' => true)),
      'CLSOLICITUDPRESTAMOESTADO_ID' => new sfWidgetFormPropelChoice(array('model' => 'ClSolicitudPrestamoEstado', 'add_empty' => true)),
      'FECHA_CREACION'               => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate())),
      'FECHA_ATENCION'               => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate())),
    ));

    $this->setValidators(array(
      'CLIENTE_ID'                   => new sfValidatorPropelChoice(array('required' => false, 'model' => 'Cliente', 'column' => 'CLIENTE_ID')),
      'USUARIO_ID'                   => new sfValidatorPropelChoice(array('required' => false, 'model' => 'Usuario', 'column' => 'USUARIO_ID')),
      'CLSOLICITUDPRESTAMOESTADO_ID' => new sfValidatorPropelChoice(array('required' => false, 'model' => 'ClSolicitudPrestamoEstado', 'column' => 'CLSOLICITUDPRESTAMOESTADO_ID')),
      'FECHA_CREACION'               => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDate(array('required' => false)), 'to_date' => new sfValidatorDate(array('required' => false)))),
      'FECHA_ATENCION'               => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDate(array('required' => false)), 'to_date' => new sfValidatorDate(array('required' => false)))),
    ));

    $this->widgetSchema->setNameFormat('cl_solicitud_prestamo_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'ClSolicitudPrestamo';
  }

  public function getFields()
  {
    return array(
      'CLSOLICITUDPRESTAMO_ID'       => 'Number',
      'CLIENTE_ID'                   => 'ForeignKey',
      'USUARIO_ID'                   => 'ForeignKey',
      'CLSOLICITUDPRESTAMOESTADO_ID' => 'ForeignKey',
      'FECHA_CREACION'               => 'Date',
      'FECHA_ATENCION'               => 'Date',
    );
  }
}
