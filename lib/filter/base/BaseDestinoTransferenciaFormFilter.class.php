<?php

/**
 * DestinoTransferencia filter form base class.
 *
 * @package    simad
 * @subpackage filter
 * @author     Your name here
 */
abstract class BaseDestinoTransferenciaFormFilter extends BaseFormFilterPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'DESCRIPCION'             => new sfWidgetFormFilterInput(),
    ));

    $this->setValidators(array(
      'DESCRIPCION'             => new sfValidatorPass(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('destino_transferencia_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'DestinoTransferencia';
  }

  public function getFields()
  {
    return array(
      'DESTINOTRANSFERENCIA_ID' => 'Number',
      'DESCRIPCION'             => 'Text',
    );
  }
}
