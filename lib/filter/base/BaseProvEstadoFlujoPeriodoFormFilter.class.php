<?php

/**
 * ProvEstadoFlujoPeriodo filter form base class.
 *
 * @package    simad
 * @subpackage filter
 * @author     Your name here
 */
abstract class BaseProvEstadoFlujoPeriodoFormFilter extends BaseFormFilterPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'PROV_ESTADO_APROBACION_ID'    => new sfWidgetFormPropelChoice(array('model' => 'ProvEstadoAprobacion', 'add_empty' => true)),
      'PROV_PERIODO_VALIDEZ_ID'      => new sfWidgetFormPropelChoice(array('model' => 'ProvPeriodoValidez', 'add_empty' => true)),
      'PROV_ITEM_FLUJO_ID'           => new sfWidgetFormPropelChoice(array('model' => 'ProvItemFlujo', 'add_empty' => true)),
      'DESCRIPCION'                  => new sfWidgetFormFilterInput(),
    ));

    $this->setValidators(array(
      'PROV_ESTADO_APROBACION_ID'    => new sfValidatorPropelChoice(array('required' => false, 'model' => 'ProvEstadoAprobacion', 'column' => 'PROV_ESTADO_APROBACION_ID')),
      'PROV_PERIODO_VALIDEZ_ID'      => new sfValidatorPropelChoice(array('required' => false, 'model' => 'ProvPeriodoValidez', 'column' => 'PROV_PERIODO_VALIDEZ_ID')),
      'PROV_ITEM_FLUJO_ID'           => new sfValidatorPropelChoice(array('required' => false, 'model' => 'ProvItemFlujo', 'column' => 'PROV_ITEM_FLUJO_ID')),
      'DESCRIPCION'                  => new sfValidatorPass(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('prov_estado_flujo_periodo_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'ProvEstadoFlujoPeriodo';
  }

  public function getFields()
  {
    return array(
      'PROV_ESTADO_FLUJO_PERIODO_ID' => 'Number',
      'PROV_ESTADO_APROBACION_ID'    => 'ForeignKey',
      'PROV_PERIODO_VALIDEZ_ID'      => 'ForeignKey',
      'PROV_ITEM_FLUJO_ID'           => 'ForeignKey',
      'DESCRIPCION'                  => 'Text',
    );
  }
}
