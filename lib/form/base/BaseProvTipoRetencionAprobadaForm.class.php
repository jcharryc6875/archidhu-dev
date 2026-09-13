<?php

/**
 * ProvTipoRetencionAprobada form base class.
 *
 * @method ProvTipoRetencionAprobada getObject() Returns the current form's model object
 *
 * @package    ##PROJECT_NAME##
 * @subpackage form
 * @author     ##AUTHOR_NAME##
 */
abstract class BaseProvTipoRetencionAprobadaForm extends BaseFormPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'PROV_TIPO_RETENCION_APROBADA_ID' => new sfWidgetFormInputHidden(),
      'PROV_PERIODO_VALIDEZ_ID'         => new sfWidgetFormPropelChoice(array('model' => 'ProvPeriodoValidez', 'add_empty' => false)),
      'PROV_TIPO_RETENCION_ID'          => new sfWidgetFormPropelChoice(array('model' => 'ProvTipoRetencion', 'add_empty' => false)),
      'FECHA_CREACION'                  => new sfWidgetFormDateTime(),
    ));

    $this->setValidators(array(
      'PROV_TIPO_RETENCION_APROBADA_ID' => new sfValidatorChoice(array('choices' => array($this->getObject()->getProvTipoRetencionAprobadaId()), 'empty_value' => $this->getObject()->getProvTipoRetencionAprobadaId(), 'required' => false)),
      'PROV_PERIODO_VALIDEZ_ID'         => new sfValidatorPropelChoice(array('model' => 'ProvPeriodoValidez', 'column' => 'PROV_PERIODO_VALIDEZ_ID')),
      'PROV_TIPO_RETENCION_ID'          => new sfValidatorPropelChoice(array('model' => 'ProvTipoRetencion', 'column' => 'PROV_TIPO_RETENCION_ID')),
      'FECHA_CREACION'                  => new sfValidatorDateTime(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('prov_tipo_retencion_aprobada[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'ProvTipoRetencionAprobada';
  }


}
