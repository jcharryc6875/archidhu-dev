<?php

/**
 * Email form base class.
 *
 * @method Email getObject() Returns the current form's model object
 *
 * @package    ##PROJECT_NAME##
 * @subpackage form
 * @author     ##AUTHOR_NAME##
 */
abstract class BaseEmailForm extends BaseFormPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'EMAIL_ID'       => new sfWidgetFormInputHidden(),
      'EMAIL_ORIGEN'   => new sfWidgetFormInputText(),
      'EMAIL_DESTINO'  => new sfWidgetFormInputText(),
      'NOMBE_ORIGEN'   => new sfWidgetFormInputText(),
      'NOMBRE_DESTINO' => new sfWidgetFormInputText(),
      'ASUNTO'         => new sfWidgetFormInputText(),
      'CONTENIDO'      => new sfWidgetFormInputText(),
      'RUTA'           => new sfWidgetFormInputText(),
      'CC'             => new sfWidgetFormInputText(),
      'MAIL_ORIGINAL'  => new sfWidgetFormInputText(),
    ));

    $this->setValidators(array(
      'EMAIL_ID'       => new sfValidatorChoice(array('choices' => array($this->getObject()->getEmailId()), 'empty_value' => $this->getObject()->getEmailId(), 'required' => false)),
      'EMAIL_ORIGEN'   => new sfValidatorString(array('max_length' => 50, 'required' => false)),
      'EMAIL_DESTINO'  => new sfValidatorString(array('max_length' => 50, 'required' => false)),
      'NOMBE_ORIGEN'   => new sfValidatorString(array('max_length' => 100, 'required' => false)),
      'NOMBRE_DESTINO' => new sfValidatorString(array('max_length' => 100, 'required' => false)),
      'ASUNTO'         => new sfValidatorString(array('max_length' => 300, 'required' => false)),
      'CONTENIDO'      => new sfValidatorString(array('max_length' => 1000, 'required' => false)),
      'RUTA'           => new sfValidatorString(array('max_length' => 1000, 'required' => false)),
      'CC'             => new sfValidatorString(array('max_length' => 1000, 'required' => false)),
      'MAIL_ORIGINAL'  => new sfValidatorString(array('max_length' => 1000, 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('email[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'Email';
  }


}
