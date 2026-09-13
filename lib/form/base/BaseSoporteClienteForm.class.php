<?php

/**
 * SoporteCliente form base class.
 *
 * @method SoporteCliente getObject() Returns the current form's model object
 *
 * @package    ##PROJECT_NAME##
 * @subpackage form
 * @author     ##AUTHOR_NAME##
 */
abstract class BaseSoporteClienteForm extends BaseFormPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'SOPORTE_CLIENTE_ID' => new sfWidgetFormInputHidden(),
      'DESCRIPCION'        => new sfWidgetFormInputText(),
    ));

    $this->setValidators(array(
      'SOPORTE_CLIENTE_ID' => new sfValidatorChoice(array('choices' => array($this->getObject()->getSoporteClienteId()), 'empty_value' => $this->getObject()->getSoporteClienteId(), 'required' => false)),
      'DESCRIPCION'        => new sfValidatorString(array('max_length' => 250, 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('soporte_cliente[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'SoporteCliente';
  }


}
