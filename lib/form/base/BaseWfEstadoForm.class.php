<?php

/**
 * WfEstado form base class.
 *
 * @package    form
 * @subpackage wf_estado
 * @version    SVN: $Id: sfPropelFormGeneratedTemplate.php 15484 2009-02-13 13:13:51Z fabien $
 */
class BaseWfEstadoForm extends BaseFormPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'wf_estado_id' => new sfWidgetFormInputHidden(),
      'descripcion'  => new sfWidgetFormInputText(),
    ));

    $this->setValidators(array(
      'wf_estado_id' => new sfValidatorPropelChoice(array('model' => 'WfEstado', 'column' => 'wf_estado_id', 'required' => false)),
      'descripcion'  => new sfValidatorString(array('max_length' => 250, 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('wf_estado[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'WfEstado';
  }


}
