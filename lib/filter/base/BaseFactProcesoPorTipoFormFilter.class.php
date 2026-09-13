<?php

/**
 * FactProcesoPorTipo filter form base class.
 *
 * @package    simad
 * @subpackage filter
 * @author     Your name here
 */
abstract class BaseFactProcesoPorTipoFormFilter extends BaseFormFilterPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'FACTURAPROCESO_ID'   => new sfWidgetFormPropelChoice(array('model' => 'FacturaProceso', 'add_empty' => true)),
      'FACTURATIPO_ID'      => new sfWidgetFormPropelChoice(array('model' => 'FacturaTipo', 'add_empty' => true)),
      'FACTURAESTADO_ID'    => new sfWidgetFormPropelChoice(array('model' => 'FacturaEstado', 'add_empty' => true)),
    ));

    $this->setValidators(array(
      'FACTURAPROCESO_ID'   => new sfValidatorPropelChoice(array('required' => false, 'model' => 'FacturaProceso', 'column' => 'FACTURAPROCESO_ID')),
      'FACTURATIPO_ID'      => new sfValidatorPropelChoice(array('required' => false, 'model' => 'FacturaTipo', 'column' => 'FACTURATIPO_ID')),
      'FACTURAESTADO_ID'    => new sfValidatorPropelChoice(array('required' => false, 'model' => 'FacturaEstado', 'column' => 'FACTURAESTADO_ID')),
    ));

    $this->widgetSchema->setNameFormat('fact_proceso_por_tipo_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'FactProcesoPorTipo';
  }

  public function getFields()
  {
    return array(
      'FACTPROCESOXTIPO_ID' => 'Number',
      'FACTURAPROCESO_ID'   => 'ForeignKey',
      'FACTURATIPO_ID'      => 'ForeignKey',
      'FACTURAESTADO_ID'    => 'ForeignKey',
    );
  }
}
