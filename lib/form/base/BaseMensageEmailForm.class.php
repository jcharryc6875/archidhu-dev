<?php

/**
 * MensageEmail form base class.
 *
 * @method MensageEmail getObject() Returns the current form's model object
 *
 * @package    ##PROJECT_NAME##
 * @subpackage form
 * @author     ##AUTHOR_NAME##
 */
abstract class BaseMensageEmailForm extends BaseFormPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'MENSAGEEMAIL_ID' => new sfWidgetFormInputHidden(),
      'FORMATO_MENSAJE' => new sfWidgetFormInputText(),
    ));

    $this->setValidators(array(
      'MENSAGEEMAIL_ID' => new sfValidatorChoice(array('choices' => array($this->getObject()->getMensageemailId()), 'empty_value' => $this->getObject()->getMensageemailId(), 'required' => false)),
      'FORMATO_MENSAJE' => new sfValidatorString(array('max_length' => 100, 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('mensage_email[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'MensageEmail';
  }


}
