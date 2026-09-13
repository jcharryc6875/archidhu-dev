<?php

/**
 * ProvCondicionExpedicion form base class.
 *
 * @method ProvCondicionExpedicion getObject() Returns the current form's model object
 *
 * @package    ##PROJECT_NAME##
 * @subpackage form
 * @author     ##AUTHOR_NAME##
 */
abstract class BaseProvCondicionExpedicionForm extends BaseFormPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'PROV_CONDICION_EXPEDICION_ID' => new sfWidgetFormInputHidden(),
      'CODIGO'                       => new sfWidgetFormInputText(),
      'DESCRIPCION'                  => new sfWidgetFormInputText(),
    ));

    $this->setValidators(array(
      'PROV_CONDICION_EXPEDICION_ID' => new sfValidatorChoice(array('choices' => array($this->getObject()->getProvCondicionExpedicionId()), 'empty_value' => $this->getObject()->getProvCondicionExpedicionId(), 'required' => false)),
      'CODIGO'                       => new sfValidatorString(array('max_length' => 50, 'required' => false)),
      'DESCRIPCION'                  => new sfValidatorString(array('max_length' => 250, 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('prov_condicion_expedicion[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'ProvCondicionExpedicion';
  }


}
