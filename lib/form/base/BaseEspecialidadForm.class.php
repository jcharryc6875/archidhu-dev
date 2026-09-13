<?php

/**
 * Especialidad form base class.
 *
 * @method Especialidad getObject() Returns the current form's model object
 *
 * @package    ##PROJECT_NAME##
 * @subpackage form
 * @author     ##AUTHOR_NAME##
 */
abstract class BaseEspecialidadForm extends BaseFormPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'ESPECIALIDAD_ID' => new sfWidgetFormInputHidden(),
      'DESCRIPCION'     => new sfWidgetFormInputText(),
    ));

    $this->setValidators(array(
      'ESPECIALIDAD_ID' => new sfValidatorChoice(array('choices' => array($this->getObject()->getEspecialidadId()), 'empty_value' => $this->getObject()->getEspecialidadId(), 'required' => false)),
      'DESCRIPCION'     => new sfValidatorString(array('max_length' => 250, 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('especialidad[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'Especialidad';
  }


}
