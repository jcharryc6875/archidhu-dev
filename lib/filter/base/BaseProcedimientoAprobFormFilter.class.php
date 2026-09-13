<?php

/**
 * ProcedimientoAprob filter form base class.
 *
 * @package    simad
 * @subpackage filter
 * @author     Your name here
 */
abstract class BaseProcedimientoAprobFormFilter extends BaseFormFilterPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'PROCEDIMIENTO_ID'      => new sfWidgetFormPropelChoice(array('model' => 'Procedimiento', 'add_empty' => true)),
      'USUARIO_ID'            => new sfWidgetFormPropelChoice(array('model' => 'Usuario', 'add_empty' => true)),
      'OBS_APROBACION'        => new sfWidgetFormFilterInput(),
      'ESTADO_APROBACION'     => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'FECHA_CREACION'        => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate())),
      'FECHA_APROBACION'      => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate())),
    ));

    $this->setValidators(array(
      'PROCEDIMIENTO_ID'      => new sfValidatorPropelChoice(array('required' => false, 'model' => 'Procedimiento', 'column' => 'PROCEDIMIENTO_ID')),
      'USUARIO_ID'            => new sfValidatorPropelChoice(array('required' => false, 'model' => 'Usuario', 'column' => 'USUARIO_ID')),
      'OBS_APROBACION'        => new sfValidatorPass(array('required' => false)),
      'ESTADO_APROBACION'     => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'FECHA_CREACION'        => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDate(array('required' => false)), 'to_date' => new sfValidatorDate(array('required' => false)))),
      'FECHA_APROBACION'      => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDate(array('required' => false)), 'to_date' => new sfValidatorDate(array('required' => false)))),
    ));

    $this->widgetSchema->setNameFormat('procedimiento_aprob_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'ProcedimientoAprob';
  }

  public function getFields()
  {
    return array(
      'PROCEDIMIENTOAPROB_ID' => 'Number',
      'PROCEDIMIENTO_ID'      => 'ForeignKey',
      'USUARIO_ID'            => 'ForeignKey',
      'OBS_APROBACION'        => 'Text',
      'ESTADO_APROBACION'     => 'Number',
      'FECHA_CREACION'        => 'Date',
      'FECHA_APROBACION'      => 'Date',
    );
  }
}
