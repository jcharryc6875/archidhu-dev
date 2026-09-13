<?php

/**
 * WfActividad form base class.
 *
 * @package    form
 * @subpackage wf_actividad
 * @version    SVN: $Id: sfPropelFormGeneratedTemplate.php 15484 2009-02-13 13:13:51Z fabien $
 */
class BaseWfActividadForm extends BaseFormPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'wf_actividad_id' => new sfWidgetFormInputHidden(),
      'descripcion'     => new sfWidgetFormInputText(),
    ));

    $this->setValidators(array(
      'wf_actividad_id' => new sfValidatorPropelChoice(array('model' => 'WfActividad', 'column' => 'wf_actividad_id', 'required' => false)),
      'descripcion'     => new sfValidatorString(array('max_length' => 250, 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('wf_actividad[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'WfActividad';
  }


}
