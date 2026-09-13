<?php

/**
 * ProvAduanaEntrada filter form base class.
 *
 * @package    simad
 * @subpackage filter
 * @author     Your name here
 */
abstract class BaseProvAduanaEntradaFormFilter extends BaseFormFilterPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'CODIGO'                 => new sfWidgetFormFilterInput(),
      'DESCRIPCION'            => new sfWidgetFormFilterInput(),
    ));

    $this->setValidators(array(
      'CODIGO'                 => new sfValidatorPass(array('required' => false)),
      'DESCRIPCION'            => new sfValidatorPass(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('prov_aduana_entrada_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'ProvAduanaEntrada';
  }

  public function getFields()
  {
    return array(
      'PROV_ADUANA_ENTRADA_ID' => 'Number',
      'CODIGO'                 => 'Text',
      'DESCRIPCION'            => 'Text',
    );
  }
}
