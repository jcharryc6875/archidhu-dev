<?php

/**
 * Factura filter form base class.
 *
 * @package    simad
 * @subpackage filter
 * @author     Your name here
 */
abstract class BaseFacturaFormFilter extends BaseFormFilterPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'PROVEEDOR_ID'        => new sfWidgetFormPropelChoice(array('model' => 'Proveedor', 'add_empty' => true)),
      'REGIONAL_ID'         => new sfWidgetFormPropelChoice(array('model' => 'Regional', 'add_empty' => true)),
      'PERIODO_ID'          => new sfWidgetFormPropelChoice(array('model' => 'Periodo', 'add_empty' => true)),
      'DEPENDENCIA_ID'      => new sfWidgetFormPropelChoice(array('model' => 'Dependencia', 'add_empty' => true)),
      'FACTURAESTADO_ID'    => new sfWidgetFormPropelChoice(array('model' => 'FacturaEstado', 'add_empty' => true)),
      'FACTURATIPO_ID'      => new sfWidgetFormPropelChoice(array('model' => 'FacturaTipo', 'add_empty' => true)),
      'FORMARECEPCION_ID'   => new sfWidgetFormPropelChoice(array('model' => 'FormaRecepcion', 'add_empty' => true)),
      'NUMERO'              => new sfWidgetFormFilterInput(),
      'VALOR_FACTURA'       => new sfWidgetFormFilterInput(),
      'VALOR_FACTURA_US'    => new sfWidgetFormFilterInput(),
      'VALOR_FACTURA_E'     => new sfWidgetFormFilterInput(),
      'CONCEPTO'            => new sfWidgetFormFilterInput(),
      'OBSERVACIONES'       => new sfWidgetFormFilterInput(),
      'FECHA_RECIBIDO'      => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate())),
      'HORA_RECIBIDO'       => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate())),
      'FECHA_RADICADO'      => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate())),
      'CODIGO_BARRAS'       => new sfWidgetFormFilterInput(),
      'ASUNTO'              => new sfWidgetFormFilterInput(),
      'RUTA'                => new sfWidgetFormFilterInput(),
      'NUMERO_RADICACION'   => new sfWidgetFormFilterInput(),
      'RADICADO'            => new sfWidgetFormFilterInput(),
      'MARCA'               => new sfWidgetFormFilterInput(),
      'MARCACAUSACION'      => new sfWidgetFormFilterInput(),
      'CAUSADAENSAP'        => new sfWidgetFormFilterInput(),
      'FECHA_VENCIMIENTO'   => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate())),
      'TIPOFIRMADIGITAL_ID' => new sfWidgetFormFilterInput(),
      'DOC_CAUSACION'       => new sfWidgetFormFilterInput(),
    ));

    $this->setValidators(array(
      'PROVEEDOR_ID'        => new sfValidatorPropelChoice(array('required' => false, 'model' => 'Proveedor', 'column' => 'PROVEEDOR_ID')),
      'REGIONAL_ID'         => new sfValidatorPropelChoice(array('required' => false, 'model' => 'Regional', 'column' => 'REGIONAL_ID')),
      'PERIODO_ID'          => new sfValidatorPropelChoice(array('required' => false, 'model' => 'Periodo', 'column' => 'PERIODO_ID')),
      'DEPENDENCIA_ID'      => new sfValidatorPropelChoice(array('required' => false, 'model' => 'Dependencia', 'column' => 'DEPENDENCIA_ID')),
      'FACTURAESTADO_ID'    => new sfValidatorPropelChoice(array('required' => false, 'model' => 'FacturaEstado', 'column' => 'FACTURAESTADO_ID')),
      'FACTURATIPO_ID'      => new sfValidatorPropelChoice(array('required' => false, 'model' => 'FacturaTipo', 'column' => 'FACTURATIPO_ID')),
      'FORMARECEPCION_ID'   => new sfValidatorPropelChoice(array('required' => false, 'model' => 'FormaRecepcion', 'column' => 'FORMARECEPCION_ID')),
      'NUMERO'              => new sfValidatorPass(array('required' => false)),
      'VALOR_FACTURA'       => new sfValidatorSchemaFilter('text', new sfValidatorNumber(array('required' => false))),
      'VALOR_FACTURA_US'    => new sfValidatorSchemaFilter('text', new sfValidatorNumber(array('required' => false))),
      'VALOR_FACTURA_E'     => new sfValidatorSchemaFilter('text', new sfValidatorNumber(array('required' => false))),
      'CONCEPTO'            => new sfValidatorPass(array('required' => false)),
      'OBSERVACIONES'       => new sfValidatorPass(array('required' => false)),
      'FECHA_RECIBIDO'      => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDate(array('required' => false)), 'to_date' => new sfValidatorDate(array('required' => false)))),
      'HORA_RECIBIDO'       => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDate(array('required' => false)), 'to_date' => new sfValidatorDate(array('required' => false)))),
      'FECHA_RADICADO'      => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDate(array('required' => false)), 'to_date' => new sfValidatorDate(array('required' => false)))),
      'CODIGO_BARRAS'       => new sfValidatorPass(array('required' => false)),
      'ASUNTO'              => new sfValidatorPass(array('required' => false)),
      'RUTA'                => new sfValidatorPass(array('required' => false)),
      'NUMERO_RADICACION'   => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'RADICADO'            => new sfValidatorPass(array('required' => false)),
      'MARCA'               => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'MARCACAUSACION'      => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'CAUSADAENSAP'        => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'FECHA_VENCIMIENTO'   => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDate(array('required' => false)), 'to_date' => new sfValidatorDate(array('required' => false)))),
      'TIPOFIRMADIGITAL_ID' => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'DOC_CAUSACION'       => new sfValidatorPass(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('factura_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'Factura';
  }

  public function getFields()
  {
    return array(
      'FACTURA_ID'          => 'Number',
      'PROVEEDOR_ID'        => 'ForeignKey',
      'REGIONAL_ID'         => 'ForeignKey',
      'PERIODO_ID'          => 'ForeignKey',
      'DEPENDENCIA_ID'      => 'ForeignKey',
      'FACTURAESTADO_ID'    => 'ForeignKey',
      'FACTURATIPO_ID'      => 'ForeignKey',
      'FORMARECEPCION_ID'   => 'ForeignKey',
      'NUMERO'              => 'Text',
      'VALOR_FACTURA'       => 'Number',
      'VALOR_FACTURA_US'    => 'Number',
      'VALOR_FACTURA_E'     => 'Number',
      'CONCEPTO'            => 'Text',
      'OBSERVACIONES'       => 'Text',
      'FECHA_RECIBIDO'      => 'Date',
      'HORA_RECIBIDO'       => 'Date',
      'FECHA_RADICADO'      => 'Date',
      'CODIGO_BARRAS'       => 'Text',
      'ASUNTO'              => 'Text',
      'RUTA'                => 'Text',
      'NUMERO_RADICACION'   => 'Number',
      'RADICADO'            => 'Text',
      'MARCA'               => 'Number',
      'MARCACAUSACION'      => 'Number',
      'CAUSADAENSAP'        => 'Number',
      'FECHA_VENCIMIENTO'   => 'Date',
      'TIPOFIRMADIGITAL_ID' => 'Number',
      'DOC_CAUSACION'       => 'Text',
    );
  }
}
