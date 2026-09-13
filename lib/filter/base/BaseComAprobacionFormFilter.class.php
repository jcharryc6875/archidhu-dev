<?php

/**
 * ComAprobacion filter form base class.
 *
 * @package    simad
 * @subpackage filter
 * @author     Your name here
 */
abstract class BaseComAprobacionFormFilter extends BaseFormFilterPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'ESTADOCOMAPROBACION_ID' => new sfWidgetFormPropelChoice(array('model' => 'EstadoComAprobacion', 'add_empty' => true)),
      'USUARIO_ID'             => new sfWidgetFormPropelChoice(array('model' => 'Usuario', 'add_empty' => true)),
      'MODULO_ID'              => new sfWidgetFormPropelChoice(array('model' => 'Modulo', 'add_empty' => true)),
      'CONSECUTIVO_ID'         => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'OBSERVACIONES'          => new sfWidgetFormFilterInput(),
      'FECHA_CREACION'         => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate())),
      'FECHA_EJECUCION'        => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate())),
    ));

    $this->setValidators(array(
      'ESTADOCOMAPROBACION_ID' => new sfValidatorPropelChoice(array('required' => false, 'model' => 'EstadoComAprobacion', 'column' => 'ESTADOCOMAPROBACION_ID')),
      'USUARIO_ID'             => new sfValidatorPropelChoice(array('required' => false, 'model' => 'Usuario', 'column' => 'USUARIO_ID')),
      'MODULO_ID'              => new sfValidatorPropelChoice(array('required' => false, 'model' => 'Modulo', 'column' => 'MODULO_ID')),
      'CONSECUTIVO_ID'         => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'OBSERVACIONES'          => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'FECHA_CREACION'         => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDate(array('required' => false)), 'to_date' => new sfValidatorDate(array('required' => false)))),
      'FECHA_EJECUCION'        => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDate(array('required' => false)), 'to_date' => new sfValidatorDate(array('required' => false)))),
    ));

    $this->widgetSchema->setNameFormat('com_aprobacion_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'ComAprobacion';
  }

  public function getFields()
  {
    return array(
      'COMAPROBACION_ID'       => 'Number',
      'ESTADOCOMAPROBACION_ID' => 'ForeignKey',
      'USUARIO_ID'             => 'ForeignKey',
      'MODULO_ID'              => 'ForeignKey',
      'CONSECUTIVO_ID'         => 'Number',
      'OBSERVACIONES'          => 'Number',
      'FECHA_CREACION'         => 'Date',
      'FECHA_EJECUCION'        => 'Date',
    );
  }
}
