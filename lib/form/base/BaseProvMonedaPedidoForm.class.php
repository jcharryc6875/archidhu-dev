<?php

/**
 * ProvMonedaPedido form base class.
 *
 * @method ProvMonedaPedido getObject() Returns the current form's model object
 *
 * @package    ##PROJECT_NAME##
 * @subpackage form
 * @author     ##AUTHOR_NAME##
 */
abstract class BaseProvMonedaPedidoForm extends BaseFormPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'PROV_MONEDA_PEDIDO_ID' => new sfWidgetFormInputHidden(),
      'NOMBRE'                => new sfWidgetFormInputText(),
      'CODIGO'                => new sfWidgetFormInputText(),
      'VALOR'                 => new sfWidgetFormInputText(),
    ));

    $this->setValidators(array(
      'PROV_MONEDA_PEDIDO_ID' => new sfValidatorChoice(array('choices' => array($this->getObject()->getProvMonedaPedidoId()), 'empty_value' => $this->getObject()->getProvMonedaPedidoId(), 'required' => false)),
      'NOMBRE'                => new sfValidatorString(array('max_length' => 100, 'required' => false)),
      'CODIGO'                => new sfValidatorString(array('max_length' => 50, 'required' => false)),
      'VALOR'                 => new sfValidatorInteger(array('min' => -9.2233720368548E+18, 'max' => 9.2233720368548E+18, 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('prov_moneda_pedido[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'ProvMonedaPedido';
  }


}
