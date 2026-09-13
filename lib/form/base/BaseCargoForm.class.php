<?php

/**
 * Cargo form base class.
 *
 * @method Cargo getObject() Returns the current form's model object
 *
 * @package    ##PROJECT_NAME##
 * @subpackage form
 * @author     ##AUTHOR_NAME##
 */
abstract class BaseCargoForm extends BaseFormPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'CARGO_ID'    => new sfWidgetFormInputHidden(),
      'DESCRIPCION' => new sfWidgetFormInputText(),
    ));

    $this->setValidators(array(
      'CARGO_ID'    => new sfValidatorChoice(array('choices' => array($this->getObject()->getCargoId()), 'empty_value' => $this->getObject()->getCargoId(), 'required' => false)),
      'DESCRIPCION' => new sfValidatorString(array('max_length' => 250, 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('cargo[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'Cargo';
  }


}
