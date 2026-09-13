<?php

/**
 * OrigenTransferencia filter form base class.
 *
 * @package    simad
 * @subpackage filter
 * @author     Your name here
 */
abstract class BaseOrigenTransferenciaFormFilter extends BaseFormFilterPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'DESCRIPCION'           => new sfWidgetFormFilterInput(),
    ));

    $this->setValidators(array(
      'DESCRIPCION'           => new sfValidatorPass(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('origen_transferencia_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'OrigenTransferencia';
  }

  public function getFields()
  {
    return array(
      'ORIGENTRANSFERENCIAID' => 'Number',
      'DESCRIPCION'           => 'Text',
    );
  }
}
