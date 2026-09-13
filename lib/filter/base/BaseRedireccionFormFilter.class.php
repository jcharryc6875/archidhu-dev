<?php

/**
 * Redireccion filter form base class.
 *
 * @package    simad
 * @subpackage filter
 * @author     Your name here
 */
abstract class BaseRedireccionFormFilter extends BaseFormFilterPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'ESTADOREDIRECCION_ID' => new sfWidgetFormPropelChoice(array('model' => 'EstadoRedireccion', 'add_empty' => true)),
      'MOTIVO'               => new sfWidgetFormFilterInput(),
      'FECHA_INICIAL'        => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate())),
      'FECHA_FINAL'          => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate())),
    ));

    $this->setValidators(array(
      'ESTADOREDIRECCION_ID' => new sfValidatorPropelChoice(array('required' => false, 'model' => 'EstadoRedireccion', 'column' => 'ESTADOREDIRECCION_ID')),
      'MOTIVO'               => new sfValidatorPass(array('required' => false)),
      'FECHA_INICIAL'        => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDate(array('required' => false)), 'to_date' => new sfValidatorDate(array('required' => false)))),
      'FECHA_FINAL'          => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDate(array('required' => false)), 'to_date' => new sfValidatorDate(array('required' => false)))),
    ));

    $this->widgetSchema->setNameFormat('redireccion_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'Redireccion';
  }

  public function getFields()
  {
    return array(
      'REDIRECCION_ID'       => 'Number',
      'ESTADOREDIRECCION_ID' => 'ForeignKey',
      'MOTIVO'               => 'Text',
      'FECHA_INICIAL'        => 'Date',
      'FECHA_FINAL'          => 'Date',
    );
  }
}
