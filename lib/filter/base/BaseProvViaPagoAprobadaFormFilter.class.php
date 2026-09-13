<?php

/**
 * ProvViaPagoAprobada filter form base class.
 *
 * @package    simad
 * @subpackage filter
 * @author     Your name here
 */
abstract class BaseProvViaPagoAprobadaFormFilter extends BaseFormFilterPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'PROV_VIA_PAGO_ID'          => new sfWidgetFormPropelChoice(array('model' => 'ProvViaPago', 'add_empty' => true)),
      'PROV_PERIODO_VALIDEZ_ID'   => new sfWidgetFormPropelChoice(array('model' => 'ProvPeriodoValidez', 'add_empty' => true)),
      'FECHA_CREACION'            => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate())),
    ));

    $this->setValidators(array(
      'PROV_VIA_PAGO_ID'          => new sfValidatorPropelChoice(array('required' => false, 'model' => 'ProvViaPago', 'column' => 'PROV_VIA_PAGO_ID')),
      'PROV_PERIODO_VALIDEZ_ID'   => new sfValidatorPropelChoice(array('required' => false, 'model' => 'ProvPeriodoValidez', 'column' => 'PROV_PERIODO_VALIDEZ_ID')),
      'FECHA_CREACION'            => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDate(array('required' => false)), 'to_date' => new sfValidatorDate(array('required' => false)))),
    ));

    $this->widgetSchema->setNameFormat('prov_via_pago_aprobada_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'ProvViaPagoAprobada';
  }

  public function getFields()
  {
    return array(
      'PROV_VIA_PAGO_APROBADA_ID' => 'Number',
      'PROV_VIA_PAGO_ID'          => 'ForeignKey',
      'PROV_PERIODO_VALIDEZ_ID'   => 'ForeignKey',
      'FECHA_CREACION'            => 'Date',
    );
  }
}
