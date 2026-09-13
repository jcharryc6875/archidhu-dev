<?php

/**
 * FacturaDevolucion filter form base class.
 *
 * @package    simad
 * @subpackage filter
 * @author     Your name here
 */
abstract class BaseFacturaDevolucionFormFilter extends BaseFormFilterPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'FAPROCESOACTUAL_ID'   => new sfWidgetFormPropelChoice(array('model' => 'FacturaProceso', 'add_empty' => true)),
      'FAESTADOACTUAL_ID'    => new sfWidgetFormPropelChoice(array('model' => 'FacturaEstado', 'add_empty' => true)),
      'FAPROCESODESTINO_ID'  => new sfWidgetFormPropelChoice(array('model' => 'FacturaProceso', 'add_empty' => true)),
      'FAESTADODESTINO_ID'   => new sfWidgetFormPropelChoice(array('model' => 'FacturaEstado', 'add_empty' => true)),
      'FACTURATIPO_ID'       => new sfWidgetFormPropelChoice(array('model' => 'FacturaTipo', 'add_empty' => true)),
    ));

    $this->setValidators(array(
      'FAPROCESOACTUAL_ID'   => new sfValidatorPropelChoice(array('required' => false, 'model' => 'FacturaProceso', 'column' => 'FACTURAPROCESO_ID')),
      'FAESTADOACTUAL_ID'    => new sfValidatorPropelChoice(array('required' => false, 'model' => 'FacturaEstado', 'column' => 'FACTURAESTADO_ID')),
      'FAPROCESODESTINO_ID'  => new sfValidatorPropelChoice(array('required' => false, 'model' => 'FacturaProceso', 'column' => 'FACTURAPROCESO_ID')),
      'FAESTADODESTINO_ID'   => new sfValidatorPropelChoice(array('required' => false, 'model' => 'FacturaEstado', 'column' => 'FACTURAESTADO_ID')),
      'FACTURATIPO_ID'       => new sfValidatorPropelChoice(array('required' => false, 'model' => 'FacturaTipo', 'column' => 'FACTURATIPO_ID')),
    ));

    $this->widgetSchema->setNameFormat('factura_devolucion_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'FacturaDevolucion';
  }

  public function getFields()
  {
    return array(
      'FACTURADEVOLUCION_ID' => 'Number',
      'FAPROCESOACTUAL_ID'   => 'ForeignKey',
      'FAESTADOACTUAL_ID'    => 'ForeignKey',
      'FAPROCESODESTINO_ID'  => 'ForeignKey',
      'FAESTADODESTINO_ID'   => 'ForeignKey',
      'FACTURATIPO_ID'       => 'ForeignKey',
    );
  }
}
