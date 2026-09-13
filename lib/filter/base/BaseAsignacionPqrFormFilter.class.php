<?php

/**
 * AsignacionPqr filter form base class.
 *
 * @package    simad
 * @subpackage filter
 * @author     Your name here
 */
abstract class BaseAsignacionPqrFormFilter extends BaseFormFilterPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'ESTADOASIGNACIONPQR_ID' => new sfWidgetFormPropelChoice(array('model' => 'EstadoAsignacionPqr', 'add_empty' => true)),
      'USUARIO_ID'             => new sfWidgetFormPropelChoice(array('model' => 'Usuario', 'add_empty' => true)),
      'PQR_ID'                 => new sfWidgetFormPropelChoice(array('model' => 'Pqr', 'add_empty' => true)),
      'FECHA_CREACION'         => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate())),
      'ESTA_ASIGNADO'          => new sfWidgetFormFilterInput(),
    ));

    $this->setValidators(array(
      'ESTADOASIGNACIONPQR_ID' => new sfValidatorPropelChoice(array('required' => false, 'model' => 'EstadoAsignacionPqr', 'column' => 'ESTADOASIGNACIONPQR_ID')),
      'USUARIO_ID'             => new sfValidatorPropelChoice(array('required' => false, 'model' => 'Usuario', 'column' => 'USUARIO_ID')),
      'PQR_ID'                 => new sfValidatorPropelChoice(array('required' => false, 'model' => 'Pqr', 'column' => 'PQR_ID')),
      'FECHA_CREACION'         => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDate(array('required' => false)), 'to_date' => new sfValidatorDate(array('required' => false)))),
      'ESTA_ASIGNADO'          => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
    ));

    $this->widgetSchema->setNameFormat('asignacion_pqr_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'AsignacionPqr';
  }

  public function getFields()
  {
    return array(
      'ASIGNACIONPQR_ID'       => 'Number',
      'ESTADOASIGNACIONPQR_ID' => 'ForeignKey',
      'USUARIO_ID'             => 'ForeignKey',
      'PQR_ID'                 => 'ForeignKey',
      'FECHA_CREACION'         => 'Date',
      'ESTA_ASIGNADO'          => 'Number',
    );
  }
}
