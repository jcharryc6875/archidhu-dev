<?php

/**
 * WfTransicion form base class.
 *
 * @package    form
 * @subpackage wf_transicion
 * @version    SVN: $Id: sfPropelFormGeneratedTemplate.php 15484 2009-02-13 13:13:51Z fabien $
 */
class BaseWfTransicionForm extends BaseFormPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'wf_transicion_id' => new sfWidgetFormInputHidden(),
      'descripcion'      => new sfWidgetFormInputText(),
    ));

    $this->setValidators(array(
      'wf_transicion_id' => new sfValidatorPropelChoice(array('model' => 'WfTransicion', 'column' => 'wf_transicion_id', 'required' => false)),
      'descripcion'      => new sfValidatorString(array('max_length' => 250, 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('wf_transicion[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'WfTransicion';
  }


}
