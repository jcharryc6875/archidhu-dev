<?php

/**
 * FacturaVitacora filter form base class.
 *
 * @package    simad
 * @subpackage filter
 * @author     Your name here
 */
abstract class BaseFacturaVitacoraFormFilter extends BaseFormFilterPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'FACTURAPROCESO_ID'        => new sfWidgetFormPropelChoice(array('model' => 'FacturaProceso', 'add_empty' => true)),
      'FACTURAESTADO_ID'         => new sfWidgetFormPropelChoice(array('model' => 'FacturaEstado', 'add_empty' => true)),
      'FACTURA_ID'               => new sfWidgetFormPropelChoice(array('model' => 'Factura', 'add_empty' => true)),
      'FACTURAVITACORAESTADO_ID' => new sfWidgetFormPropelChoice(array('model' => 'FacturaVitacoraEstado', 'add_empty' => true)),
      'FECHA_I'                  => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate())),
      'FECHA_I_DATE'             => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate())),
      'FECHA_F'                  => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate())),
      'FECHA_F_DATE'             => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate())),
      'OBSERVACIONES'            => new sfWidgetFormFilterInput(),
      'EJECUTADA'                => new sfWidgetFormFilterInput(),
    ));

    $this->setValidators(array(
      'FACTURAPROCESO_ID'        => new sfValidatorPropelChoice(array('required' => false, 'model' => 'FacturaProceso', 'column' => 'FACTURAPROCESO_ID')),
      'FACTURAESTADO_ID'         => new sfValidatorPropelChoice(array('required' => false, 'model' => 'FacturaEstado', 'column' => 'FACTURAESTADO_ID')),
      'FACTURA_ID'               => new sfValidatorPropelChoice(array('required' => false, 'model' => 'Factura', 'column' => 'FACTURA_ID')),
      'FACTURAVITACORAESTADO_ID' => new sfValidatorPropelChoice(array('required' => false, 'model' => 'FacturaVitacoraEstado', 'column' => 'FACTURAVITACORAESTADO_ID')),
      'FECHA_I'                  => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDate(array('required' => false)), 'to_date' => new sfValidatorDate(array('required' => false)))),
      'FECHA_I_DATE'             => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDate(array('required' => false)), 'to_date' => new sfValidatorDate(array('required' => false)))),
      'FECHA_F'                  => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDate(array('required' => false)), 'to_date' => new sfValidatorDate(array('required' => false)))),
      'FECHA_F_DATE'             => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDate(array('required' => false)), 'to_date' => new sfValidatorDate(array('required' => false)))),
      'OBSERVACIONES'            => new sfValidatorPass(array('required' => false)),
      'EJECUTADA'                => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
    ));

    $this->widgetSchema->setNameFormat('factura_vitacora_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'FacturaVitacora';
  }

  public function getFields()
  {
    return array(
      'FACTURAVITACORA_ID'       => 'Number',
      'FACTURAPROCESO_ID'        => 'ForeignKey',
      'FACTURAESTADO_ID'         => 'ForeignKey',
      'FACTURA_ID'               => 'ForeignKey',
      'FACTURAVITACORAESTADO_ID' => 'ForeignKey',
      'FECHA_I'                  => 'Date',
      'FECHA_I_DATE'             => 'Date',
      'FECHA_F'                  => 'Date',
      'FECHA_F_DATE'             => 'Date',
      'OBSERVACIONES'            => 'Text',
      'EJECUTADA'                => 'Number',
    );
  }
}
