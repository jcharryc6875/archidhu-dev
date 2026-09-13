<?php

/**
 * VerificacionContcliente form base class.
 *
 * @method VerificacionContcliente getObject() Returns the current form's model object
 *
 * @package    ##PROJECT_NAME##
 * @subpackage form
 * @author     ##AUTHOR_NAME##
 */
abstract class BaseVerificacionContclienteForm extends BaseFormPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'VERIFICACIONCONTCLIENTE_ID' => new sfWidgetFormInputHidden(),
      'DESCRIPCION'                => new sfWidgetFormInputText(),
    ));

    $this->setValidators(array(
      'VERIFICACIONCONTCLIENTE_ID' => new sfValidatorChoice(array('choices' => array($this->getObject()->getVerificacioncontclienteId()), 'empty_value' => $this->getObject()->getVerificacioncontclienteId(), 'required' => false)),
      'DESCRIPCION'                => new sfValidatorString(array('max_length' => 250, 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('verificacion_contcliente[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'VerificacionContcliente';
  }


}
