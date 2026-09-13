<?php

/**
 * WfVariable form base class.
 *
 * @package    form
 * @subpackage wf_variable
 * @version    SVN: $Id: sfPropelFormGeneratedTemplate.php 15484 2009-02-13 13:13:51Z fabien $
 */
class BaseWfVariableForm extends BaseFormPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'wfvariable_id'            => new sfWidgetFormInputHidden(),
      'wfactividadtransicion_id' => new sfWidgetFormPropelSelect(array('model' => 'WfActividadTransicion', 'add_empty' => false)),
      'wftipodato_id'            => new sfWidgetFormPropelSelect(array('model' => 'WfTipoDato', 'add_empty' => true)),
      'nombre'                   => new sfWidgetFormInputText(),
      'orden'                    => new sfWidgetFormInputText(),
    ));

    $this->setValidators(array(
      'wfvariable_id'            => new sfValidatorPropelChoice(array('model' => 'WfVariable', 'column' => 'wfvariable_id', 'required' => false)),
      'wfactividadtransicion_id' => new sfValidatorPropelChoice(array('model' => 'WfActividadTransicion', 'column' => 'wfactividadtransicion_id')),
      'wftipodato_id'            => new sfValidatorPropelChoice(array('model' => 'WfTipoDato', 'column' => 'wftipodato_id', 'required' => false)),
      'nombre'                   => new sfValidatorString(array('max_length' => 100, 'required' => false)),
      'orden'                    => new sfValidatorInteger(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('wf_variable[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'WfVariable';
  }


}
