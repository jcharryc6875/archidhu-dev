<?php

/**
 * WfScript form base class.
 *
 * @package    form
 * @subpackage wf_script
 * @version    SVN: $Id: sfPropelFormGeneratedTemplate.php 15484 2009-02-13 13:13:51Z fabien $
 */
class BaseWfScriptForm extends BaseFormPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'wfscript_id' => new sfWidgetFormInputHidden(),
      'descripcion' => new sfWidgetFormInputText(),
      'contenido'   => new sfWidgetFormInputText(),
    ));

    $this->setValidators(array(
      'wfscript_id' => new sfValidatorPropelChoice(array('model' => 'WfScript', 'column' => 'wfscript_id', 'required' => false)),
      'descripcion' => new sfValidatorString(array('max_length' => 250, 'required' => false)),
      'contenido'   => new sfValidatorString(array('max_length' => 1000, 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('wf_script[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'WfScript';
  }


}
