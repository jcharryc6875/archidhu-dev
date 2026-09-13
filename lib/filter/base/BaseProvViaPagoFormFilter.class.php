<?php

/**
 * ProvViaPago filter form base class.
 *
 * @package    simad
 * @subpackage filter
 * @author     Your name here
 */
abstract class BaseProvViaPagoFormFilter extends BaseFormFilterPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'PAIS_ID'          => new sfWidgetFormPropelChoice(array('model' => 'Pais', 'add_empty' => true)),
      'DESCRIPCION'      => new sfWidgetFormFilterInput(),
      'CODIGO'           => new sfWidgetFormFilterInput(),
    ));

    $this->setValidators(array(
      'PAIS_ID'          => new sfValidatorPropelChoice(array('required' => false, 'model' => 'Pais', 'column' => 'PAIS_ID')),
      'DESCRIPCION'      => new sfValidatorPass(array('required' => false)),
      'CODIGO'           => new sfValidatorPass(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('prov_via_pago_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'ProvViaPago';
  }

  public function getFields()
  {
    return array(
      'PROV_VIA_PAGO_ID' => 'Number',
      'PAIS_ID'          => 'ForeignKey',
      'DESCRIPCION'      => 'Text',
      'CODIGO'           => 'Text',
    );
  }
}
