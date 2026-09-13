<?php

/**
 * MedioRespuesta form base class.
 *
 * @method MedioRespuesta getObject() Returns the current form's model object
 *
 * @package    ##PROJECT_NAME##
 * @subpackage form
 * @author     ##AUTHOR_NAME##
 */
abstract class BaseMedioRespuestaForm extends BaseFormPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'MEDIORESPUESTA_ID' => new sfWidgetFormInputHidden(),
      'DESCRIPCION'       => new sfWidgetFormInputText(),
      'ES_VISIBLE'        => new sfWidgetFormInputText(),
    ));

    $this->setValidators(array(
      'MEDIORESPUESTA_ID' => new sfValidatorChoice(array('choices' => array($this->getObject()->getMediorespuestaId()), 'empty_value' => $this->getObject()->getMediorespuestaId(), 'required' => false)),
      'DESCRIPCION'       => new sfValidatorString(array('max_length' => 30, 'required' => false)),
      'ES_VISIBLE'        => new sfValidatorInteger(array('min' => -128, 'max' => 127, 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('medio_respuesta[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'MedioRespuesta';
  }


}
