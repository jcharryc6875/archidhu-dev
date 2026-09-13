<?php

/**
 * WfBitacoraVariable form base class.
 *
 * @package    form
 * @subpackage wf_bitacora_variable
 * @version    SVN: $Id: sfPropelFormGeneratedTemplate.php 15484 2009-02-13 13:13:51Z fabien $
 */
class BaseWfBitacoraVariableForm extends BaseFormPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'wfbitacoravariable_id'  => new sfWidgetFormInputHidden(),
      'wfvariable_id'          => new sfWidgetFormPropelSelect(array('model' => 'WfVariable', 'add_empty' => true)),
      'wfinstanciabitacora_id' => new sfWidgetFormPropelSelect(array('model' => 'WfInstanciaBitacora', 'add_empty' => true)),
      'valor_variable'         => new sfWidgetFormTextarea(),
    ));

    $this->setValidators(array(
      'wfbitacoravariable_id'  => new sfValidatorPropelChoice(array('model' => 'WfBitacoraVariable', 'column' => 'wfbitacoravariable_id', 'required' => false)),
      'wfvariable_id'          => new sfValidatorPropelChoice(array('model' => 'WfVariable', 'column' => 'wfvariable_id', 'required' => false)),
      'wfinstanciabitacora_id' => new sfValidatorPropelChoice(array('model' => 'WfInstanciaBitacora', 'column' => 'wfinstanciabitacora_id', 'required' => false)),
      'valor_variable'         => new sfValidatorString(array('max_length' => 2147483647, 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('wf_bitacora_variable[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'WfBitacoraVariable';
  }


}
