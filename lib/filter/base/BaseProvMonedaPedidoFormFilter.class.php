<?php

/**
 * ProvMonedaPedido filter form base class.
 *
 * @package    simad
 * @subpackage filter
 * @author     Your name here
 */
abstract class BaseProvMonedaPedidoFormFilter extends BaseFormFilterPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'NOMBRE'                => new sfWidgetFormFilterInput(),
      'CODIGO'                => new sfWidgetFormFilterInput(),
      'VALOR'                 => new sfWidgetFormFilterInput(),
    ));

    $this->setValidators(array(
      'NOMBRE'                => new sfValidatorPass(array('required' => false)),
      'CODIGO'                => new sfValidatorPass(array('required' => false)),
      'VALOR'                 => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
    ));

    $this->widgetSchema->setNameFormat('prov_moneda_pedido_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'ProvMonedaPedido';
  }

  public function getFields()
  {
    return array(
      'PROV_MONEDA_PEDIDO_ID' => 'Number',
      'NOMBRE'                => 'Text',
      'CODIGO'                => 'Text',
      'VALOR'                 => 'Number',
    );
  }
}
