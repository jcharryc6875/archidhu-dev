<?php

/**
 * FormaRecepcion form base class.
 *
 * @method FormaRecepcion getObject() Returns the current form's model object
 *
 * @package    ##PROJECT_NAME##
 * @subpackage form
 * @author     ##AUTHOR_NAME##
 */
abstract class BaseFormaRecepcionForm extends BaseFormPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'FORMARECEPCION_ID'  => new sfWidgetFormInputHidden(),
      'DESCRIPCION'        => new sfWidgetFormInputText(),
      'VENTANILLA_DEFAULT' => new sfWidgetFormInputText(),
    ));

    $this->setValidators(array(
      'FORMARECEPCION_ID'  => new sfValidatorChoice(array('choices' => array($this->getObject()->getFormarecepcionId()), 'empty_value' => $this->getObject()->getFormarecepcionId(), 'required' => false)),
      'DESCRIPCION'        => new sfValidatorString(array('max_length' => 250, 'required' => false)),
      'VENTANILLA_DEFAULT' => new sfValidatorInteger(array('min' => -128, 'max' => 127, 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('forma_recepcion[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'FormaRecepcion';
  }


}
