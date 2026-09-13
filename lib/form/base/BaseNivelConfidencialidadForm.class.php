<?php

/**
 * NivelConfidencialidad form base class.
 *
 * @method NivelConfidencialidad getObject() Returns the current form's model object
 *
 * @package    simad
 * @subpackage form
 * @author     Your name here
 */
abstract class BaseNivelConfidencialidadForm extends BaseFormPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'NIVELCONFIDENCIALIDAD_ID' => new sfWidgetFormInputHidden(),
      'DESCRIPCION'              => new sfWidgetFormInputText(),
      'DEFINICION'               => new sfWidgetFormInputText(),
      'ES_VISIBLE'               => new sfWidgetFormInputText(),
    ));

    $this->setValidators(array(
      'NIVELCONFIDENCIALIDAD_ID' => new sfValidatorChoice(array('choices' => array($this->getObject()->getNivelconfidencialidadId()), 'empty_value' => $this->getObject()->getNivelconfidencialidadId(), 'required' => false)),
      'DESCRIPCION'              => new sfValidatorString(array('max_length' => 50, 'required' => false)),
      'DEFINICION'               => new sfValidatorString(array('max_length' => 50, 'required' => false)),
      'ES_VISIBLE'               => new sfValidatorInteger(array('min' => -128, 'max' => 127, 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('nivel_confidencialidad[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'NivelConfidencialidad';
  }


}
