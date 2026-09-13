<?php

/**
 * WfBuzon form base class.
 *
 * @package    form
 * @subpackage wf_buzon
 * @version    SVN: $Id: sfPropelFormGeneratedTemplate.php 15484 2009-02-13 13:13:51Z fabien $
 */
class BaseWfBuzonForm extends BaseFormPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'wf_buzon_id' => new sfWidgetFormInputHidden(),
      'descripcion' => new sfWidgetFormInputText(),
    ));

    $this->setValidators(array(
      'wf_buzon_id' => new sfValidatorPropelChoice(array('model' => 'WfBuzon', 'column' => 'wf_buzon_id', 'required' => false)),
      'descripcion' => new sfValidatorString(array('max_length' => 250, 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('wf_buzon[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'WfBuzon';
  }


}
