<?php

/**
 * ProvViaPagoAprobada form base class.
 *
 * @method ProvViaPagoAprobada getObject() Returns the current form's model object
 *
 * @package    ##PROJECT_NAME##
 * @subpackage form
 * @author     ##AUTHOR_NAME##
 */
abstract class BaseProvViaPagoAprobadaForm extends BaseFormPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'PROV_VIA_PAGO_APROBADA_ID' => new sfWidgetFormInputHidden(),
      'PROV_VIA_PAGO_ID'          => new sfWidgetFormPropelChoice(array('model' => 'ProvViaPago', 'add_empty' => false)),
      'PROV_PERIODO_VALIDEZ_ID'   => new sfWidgetFormPropelChoice(array('model' => 'ProvPeriodoValidez', 'add_empty' => false)),
      'FECHA_CREACION'            => new sfWidgetFormDateTime(),
    ));

    $this->setValidators(array(
      'PROV_VIA_PAGO_APROBADA_ID' => new sfValidatorChoice(array('choices' => array($this->getObject()->getProvViaPagoAprobadaId()), 'empty_value' => $this->getObject()->getProvViaPagoAprobadaId(), 'required' => false)),
      'PROV_VIA_PAGO_ID'          => new sfValidatorPropelChoice(array('model' => 'ProvViaPago', 'column' => 'PROV_VIA_PAGO_ID')),
      'PROV_PERIODO_VALIDEZ_ID'   => new sfValidatorPropelChoice(array('model' => 'ProvPeriodoValidez', 'column' => 'PROV_PERIODO_VALIDEZ_ID')),
      'FECHA_CREACION'            => new sfValidatorDateTime(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('prov_via_pago_aprobada[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'ProvViaPagoAprobada';
  }


}
