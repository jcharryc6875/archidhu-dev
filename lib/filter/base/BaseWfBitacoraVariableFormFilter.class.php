<?php

/**
 * WfBitacoraVariable filter form base class.
 *
 * @package    simad
 * @subpackage filter
 * @author     Your name here
 */
abstract class BaseWfBitacoraVariableFormFilter extends BaseFormFilterPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'WFVARIABLE_ID'          => new sfWidgetFormPropelChoice(array('model' => 'WfVariable', 'add_empty' => true)),
      'WFINSTANCIABITACORA_ID' => new sfWidgetFormPropelChoice(array('model' => 'WfInstanciaBitacora', 'add_empty' => true)),
      'VALOR_VARIABLE'         => new sfWidgetFormFilterInput(),
    ));

    $this->setValidators(array(
      'WFVARIABLE_ID'          => new sfValidatorPropelChoice(array('required' => false, 'model' => 'WfVariable', 'column' => 'WFVARIABLE_ID')),
      'WFINSTANCIABITACORA_ID' => new sfValidatorPropelChoice(array('required' => false, 'model' => 'WfInstanciaBitacora', 'column' => 'WFINSTANCIABITACORA_ID')),
      'VALOR_VARIABLE'         => new sfValidatorPass(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('wf_bitacora_variable_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'WfBitacoraVariable';
  }

  public function getFields()
  {
    return array(
      'WFBITACORAVARIABLE_ID'  => 'Number',
      'WFVARIABLE_ID'          => 'ForeignKey',
      'WFINSTANCIABITACORA_ID' => 'ForeignKey',
      'VALOR_VARIABLE'         => 'Text',
    );
  }
}
