<?php

/**
 * ProvTipoRetencionAprobada filter form base class.
 *
 * @package    simad
 * @subpackage filter
 * @author     Your name here
 */
abstract class BaseProvTipoRetencionAprobadaFormFilter extends BaseFormFilterPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'PROV_PERIODO_VALIDEZ_ID'         => new sfWidgetFormPropelChoice(array('model' => 'ProvPeriodoValidez', 'add_empty' => true)),
      'PROV_TIPO_RETENCION_ID'          => new sfWidgetFormPropelChoice(array('model' => 'ProvTipoRetencion', 'add_empty' => true)),
      'FECHA_CREACION'                  => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate())),
    ));

    $this->setValidators(array(
      'PROV_PERIODO_VALIDEZ_ID'         => new sfValidatorPropelChoice(array('required' => false, 'model' => 'ProvPeriodoValidez', 'column' => 'PROV_PERIODO_VALIDEZ_ID')),
      'PROV_TIPO_RETENCION_ID'          => new sfValidatorPropelChoice(array('required' => false, 'model' => 'ProvTipoRetencion', 'column' => 'PROV_TIPO_RETENCION_ID')),
      'FECHA_CREACION'                  => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDate(array('required' => false)), 'to_date' => new sfValidatorDate(array('required' => false)))),
    ));

    $this->widgetSchema->setNameFormat('prov_tipo_retencion_aprobada_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'ProvTipoRetencionAprobada';
  }

  public function getFields()
  {
    return array(
      'PROV_TIPO_RETENCION_APROBADA_ID' => 'Number',
      'PROV_PERIODO_VALIDEZ_ID'         => 'ForeignKey',
      'PROV_TIPO_RETENCION_ID'          => 'ForeignKey',
      'FECHA_CREACION'                  => 'Date',
    );
  }
}
