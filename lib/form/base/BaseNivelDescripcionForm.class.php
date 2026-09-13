<?php

/**
 * NivelDescripcion form base class.
 *
 * @method NivelDescripcion getObject() Returns the current form's model object
 *
 * @package    ##PROJECT_NAME##
 * @subpackage form
 * @author     ##AUTHOR_NAME##
 */
abstract class BaseNivelDescripcionForm extends BaseFormPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'NIVELDESCRIPCION_ID' => new sfWidgetFormInputHidden(),
      'DESCRIPCION'         => new sfWidgetFormInputText(),
    ));

    $this->setValidators(array(
      'NIVELDESCRIPCION_ID' => new sfValidatorChoice(array('choices' => array($this->getObject()->getNiveldescripcionId()), 'empty_value' => $this->getObject()->getNiveldescripcionId(), 'required' => false)),
      'DESCRIPCION'         => new sfValidatorString(array('max_length' => 250, 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('nivel_descripcion[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'NivelDescripcion';
  }


}
