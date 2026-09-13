<?php

/**
 * EstadoDigitalizacion form base class.
 *
 * @method EstadoDigitalizacion getObject() Returns the current form's model object
 *
 * @package    ##PROJECT_NAME##
 * @subpackage form
 * @author     ##AUTHOR_NAME##
 */
abstract class BaseEstadoDigitalizacionForm extends BaseFormPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'ESTADODIGITALIZACION_ID' => new sfWidgetFormInputHidden(),
      'DESCRIPCION'             => new sfWidgetFormInputText(),
    ));

    $this->setValidators(array(
      'ESTADODIGITALIZACION_ID' => new sfValidatorChoice(array('choices' => array($this->getObject()->getEstadodigitalizacionId()), 'empty_value' => $this->getObject()->getEstadodigitalizacionId(), 'required' => false)),
      'DESCRIPCION'             => new sfValidatorString(array('max_length' => 250, 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('estado_digitalizacion[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'EstadoDigitalizacion';
  }


}
