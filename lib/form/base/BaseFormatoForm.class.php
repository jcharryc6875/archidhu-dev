<?php

/**
 * Formato form base class.
 *
 * @method Formato getObject() Returns the current form's model object
 *
 * @package    ##PROJECT_NAME##
 * @subpackage form
 * @author     ##AUTHOR_NAME##
 */
abstract class BaseFormatoForm extends BaseFormPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'FORMATO_ID'  => new sfWidgetFormInputHidden(),
      'DESCRIPCION' => new sfWidgetFormInputText(),
    ));

    $this->setValidators(array(
      'FORMATO_ID'  => new sfValidatorChoice(array('choices' => array($this->getObject()->getFormatoId()), 'empty_value' => $this->getObject()->getFormatoId(), 'required' => false)),
      'DESCRIPCION' => new sfValidatorString(array('max_length' => 250, 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('formato[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'Formato';
  }


}
