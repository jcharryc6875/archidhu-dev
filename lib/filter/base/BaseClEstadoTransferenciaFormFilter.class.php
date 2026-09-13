<?php

/**
 * ClEstadoTransferencia filter form base class.
 *
 * @package    simad
 * @subpackage filter
 * @author     Your name here
 */
abstract class BaseClEstadoTransferenciaFormFilter extends BaseFormFilterPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'DESCRIPCION'              => new sfWidgetFormFilterInput(),
    ));

    $this->setValidators(array(
      'DESCRIPCION'              => new sfValidatorPass(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('cl_estado_transferencia_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'ClEstadoTransferencia';
  }

  public function getFields()
  {
    return array(
      'CLESTADOTRANSFERENCIA_ID' => 'Number',
      'DESCRIPCION'              => 'Text',
    );
  }
}
