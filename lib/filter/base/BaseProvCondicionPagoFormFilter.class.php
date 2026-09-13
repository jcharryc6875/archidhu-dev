<?php

/**
 * ProvCondicionPago filter form base class.
 *
 * @package    simad
 * @subpackage filter
 * @author     Your name here
 */
abstract class BaseProvCondicionPagoFormFilter extends BaseFormFilterPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'DESCRIPCION'            => new sfWidgetFormFilterInput(),
      'CODIGO'                 => new sfWidgetFormFilterInput(),
    ));

    $this->setValidators(array(
      'DESCRIPCION'            => new sfValidatorPass(array('required' => false)),
      'CODIGO'                 => new sfValidatorPass(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('prov_condicion_pago_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'ProvCondicionPago';
  }

  public function getFields()
  {
    return array(
      'PROV_CONDICION_PAGO_ID' => 'Number',
      'DESCRIPCION'            => 'Text',
      'CODIGO'                 => 'Text',
    );
  }
}
