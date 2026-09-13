<?php

/**
 * ClDestinoTransferencia filter form base class.
 *
 * @package    simad
 * @subpackage filter
 * @author     Your name here
 */
abstract class BaseClDestinoTransferenciaFormFilter extends BaseFormFilterPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'DESCRIPCION'               => new sfWidgetFormFilterInput(),
    ));

    $this->setValidators(array(
      'DESCRIPCION'               => new sfValidatorPass(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('cl_destino_transferencia_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'ClDestinoTransferencia';
  }

  public function getFields()
  {
    return array(
      'CLDESTINOTRANSFERENCIA_ID' => 'Number',
      'DESCRIPCION'               => 'Text',
    );
  }
}
