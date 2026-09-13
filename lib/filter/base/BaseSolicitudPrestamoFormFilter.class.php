<?php

/**
 * SolicitudPrestamo filter form base class.
 *
 * @package    simad
 * @subpackage filter
 * @author     Your name here
 */
abstract class BaseSolicitudPrestamoFormFilter extends BaseFormFilterPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'USUARIO_ID'                   => new sfWidgetFormPropelChoice(array('model' => 'Usuario', 'add_empty' => true)),
      'SOLICITUDPRESTAMOESTADO_ID'   => new sfWidgetFormPropelChoice(array('model' => 'SolicitudPrestamoEstado', 'add_empty' => true)),
      'UNIDADDOCUMENTAL_ID'          => new sfWidgetFormPropelChoice(array('model' => 'UnidadDocumental', 'add_empty' => true)),
      'CONTENIDOUNIDADDOCUMENTAL_ID' => new sfWidgetFormPropelChoice(array('model' => 'ContenidoUnidadDocumental', 'add_empty' => true)),
      'FECHA_CREACION'               => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate())),
      'FECHA_ATENCION'               => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate())),
      'OBSERVACIONES'                => new sfWidgetFormFilterInput(),
      'MARCA'                        => new sfWidgetFormFilterInput(),
    ));

    $this->setValidators(array(
      'USUARIO_ID'                   => new sfValidatorPropelChoice(array('required' => false, 'model' => 'Usuario', 'column' => 'USUARIO_ID')),
      'SOLICITUDPRESTAMOESTADO_ID'   => new sfValidatorPropelChoice(array('required' => false, 'model' => 'SolicitudPrestamoEstado', 'column' => 'SOLICITUDPRESTAMOESTADO_ID')),
      'UNIDADDOCUMENTAL_ID'          => new sfValidatorPropelChoice(array('required' => false, 'model' => 'UnidadDocumental', 'column' => 'UNIDADDOCUMENTAL_ID')),
      'CONTENIDOUNIDADDOCUMENTAL_ID' => new sfValidatorPropelChoice(array('required' => false, 'model' => 'ContenidoUnidadDocumental', 'column' => 'CONTENIDOUNIDADDOCUMENTAL_ID')),
      'FECHA_CREACION'               => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDate(array('required' => false)), 'to_date' => new sfValidatorDate(array('required' => false)))),
      'FECHA_ATENCION'               => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDate(array('required' => false)), 'to_date' => new sfValidatorDate(array('required' => false)))),
      'OBSERVACIONES'                => new sfValidatorPass(array('required' => false)),
      'MARCA'                        => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
    ));

    $this->widgetSchema->setNameFormat('solicitud_prestamo_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'SolicitudPrestamo';
  }

  public function getFields()
  {
    return array(
      'SOLICITUDPRESTAMO_ID'         => 'Number',
      'USUARIO_ID'                   => 'ForeignKey',
      'SOLICITUDPRESTAMOESTADO_ID'   => 'ForeignKey',
      'UNIDADDOCUMENTAL_ID'          => 'ForeignKey',
      'CONTENIDOUNIDADDOCUMENTAL_ID' => 'ForeignKey',
      'FECHA_CREACION'               => 'Date',
      'FECHA_ATENCION'               => 'Date',
      'OBSERVACIONES'                => 'Text',
      'MARCA'                        => 'Number',
    );
  }
}
