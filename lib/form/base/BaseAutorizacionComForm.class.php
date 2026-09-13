<?php

/**
 * AutorizacionCom form base class.
 *
 * @method AutorizacionCom getObject() Returns the current form's model object
 *
 * @package    ##PROJECT_NAME##
 * @subpackage form
 * @author     ##AUTHOR_NAME##
 */
abstract class BaseAutorizacionComForm extends BaseFormPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'AUTORIZACIONCOM_ID'    => new sfWidgetFormInputHidden(),
      'FECHA_INICIAL_AUT_COM' => new sfWidgetFormDateTime(),
      'FECHA_FINAL_AUT_COM'   => new sfWidgetFormDateTime(),
    ));

    $this->setValidators(array(
      'AUTORIZACIONCOM_ID'    => new sfValidatorChoice(array('choices' => array($this->getObject()->getAutorizacioncomId()), 'empty_value' => $this->getObject()->getAutorizacioncomId(), 'required' => false)),
      'FECHA_INICIAL_AUT_COM' => new sfValidatorDateTime(array('required' => false)),
      'FECHA_FINAL_AUT_COM'   => new sfValidatorDateTime(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('autorizacion_com[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'AutorizacionCom';
  }


}
