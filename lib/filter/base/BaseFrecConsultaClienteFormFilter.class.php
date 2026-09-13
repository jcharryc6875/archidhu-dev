<?php

/**
 * FrecConsultaCliente filter form base class.
 *
 * @package    simad
 * @subpackage filter
 * @author     Your name here
 */
abstract class BaseFrecConsultaClienteFormFilter extends BaseFormFilterPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'DESCRIPCION'            => new sfWidgetFormFilterInput(),
    ));

    $this->setValidators(array(
      'DESCRIPCION'            => new sfValidatorPass(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('frec_consulta_cliente_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'FrecConsultaCliente';
  }

  public function getFields()
  {
    return array(
      'FRECCONSULTACLIENTE_ID' => 'Number',
      'DESCRIPCION'            => 'Text',
    );
  }
}
