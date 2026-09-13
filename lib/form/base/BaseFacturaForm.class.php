<?php

/**
 * Factura form base class.
 *
 * @method Factura getObject() Returns the current form's model object
 *
 * @package    ##PROJECT_NAME##
 * @subpackage form
 * @author     ##AUTHOR_NAME##
 */
abstract class BaseFacturaForm extends BaseFormPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'FACTURA_ID'          => new sfWidgetFormInputHidden(),
      'PROVEEDOR_ID'        => new sfWidgetFormPropelChoice(array('model' => 'Proveedor', 'add_empty' => false)),
      'REGIONAL_ID'         => new sfWidgetFormPropelChoice(array('model' => 'Regional', 'add_empty' => false)),
      'PERIODO_ID'          => new sfWidgetFormPropelChoice(array('model' => 'Periodo', 'add_empty' => false)),
      'DEPENDENCIA_ID'      => new sfWidgetFormPropelChoice(array('model' => 'Dependencia', 'add_empty' => false)),
      'FACTURAESTADO_ID'    => new sfWidgetFormPropelChoice(array('model' => 'FacturaEstado', 'add_empty' => false)),
      'FACTURATIPO_ID'      => new sfWidgetFormPropelChoice(array('model' => 'FacturaTipo', 'add_empty' => false)),
      'FORMARECEPCION_ID'   => new sfWidgetFormPropelChoice(array('model' => 'FormaRecepcion', 'add_empty' => false)),
      'NUMERO'              => new sfWidgetFormInputText(),
      'VALOR_FACTURA'       => new sfWidgetFormInputText(),
      'VALOR_FACTURA_US'    => new sfWidgetFormInputText(),
      'VALOR_FACTURA_E'     => new sfWidgetFormInputText(),
      'CONCEPTO'            => new sfWidgetFormInputText(),
      'OBSERVACIONES'       => new sfWidgetFormInputText(),
      'FECHA_RECIBIDO'      => new sfWidgetFormDateTime(),
      'HORA_RECIBIDO'       => new sfWidgetFormDateTime(),
      'FECHA_RADICADO'      => new sfWidgetFormDateTime(),
      'CODIGO_BARRAS'       => new sfWidgetFormInputText(),
      'ASUNTO'              => new sfWidgetFormInputText(),
      'RUTA'                => new sfWidgetFormTextarea(),
      'NUMERO_RADICACION'   => new sfWidgetFormInputText(),
      'RADICADO'            => new sfWidgetFormInputText(),
      'MARCA'               => new sfWidgetFormInputText(),
      'MARCACAUSACION'      => new sfWidgetFormInputText(),
      'CAUSADAENSAP'        => new sfWidgetFormInputText(),
      'FECHA_VENCIMIENTO'   => new sfWidgetFormDateTime(),
      'TIPOFIRMADIGITAL_ID' => new sfWidgetFormInputText(),
      'DOC_CAUSACION'       => new sfWidgetFormInputText(),
    ));

    $this->setValidators(array(
      'FACTURA_ID'          => new sfValidatorChoice(array('choices' => array($this->getObject()->getFacturaId()), 'empty_value' => $this->getObject()->getFacturaId(), 'required' => false)),
      'PROVEEDOR_ID'        => new sfValidatorPropelChoice(array('model' => 'Proveedor', 'column' => 'PROVEEDOR_ID')),
      'REGIONAL_ID'         => new sfValidatorPropelChoice(array('model' => 'Regional', 'column' => 'REGIONAL_ID')),
      'PERIODO_ID'          => new sfValidatorPropelChoice(array('model' => 'Periodo', 'column' => 'PERIODO_ID')),
      'DEPENDENCIA_ID'      => new sfValidatorPropelChoice(array('model' => 'Dependencia', 'column' => 'DEPENDENCIA_ID')),
      'FACTURAESTADO_ID'    => new sfValidatorPropelChoice(array('model' => 'FacturaEstado', 'column' => 'FACTURAESTADO_ID')),
      'FACTURATIPO_ID'      => new sfValidatorPropelChoice(array('model' => 'FacturaTipo', 'column' => 'FACTURATIPO_ID')),
      'FORMARECEPCION_ID'   => new sfValidatorPropelChoice(array('model' => 'FormaRecepcion', 'column' => 'FORMARECEPCION_ID')),
      'NUMERO'              => new sfValidatorString(array('max_length' => 50, 'required' => false)),
      'VALOR_FACTURA'       => new sfValidatorNumber(array('required' => false)),
      'VALOR_FACTURA_US'    => new sfValidatorNumber(array('required' => false)),
      'VALOR_FACTURA_E'     => new sfValidatorNumber(array('required' => false)),
      'CONCEPTO'            => new sfValidatorString(array('max_length' => 400, 'required' => false)),
      'OBSERVACIONES'       => new sfValidatorString(array('max_length' => 4000, 'required' => false)),
      'FECHA_RECIBIDO'      => new sfValidatorDateTime(array('required' => false)),
      'HORA_RECIBIDO'       => new sfValidatorDateTime(array('required' => false)),
      'FECHA_RADICADO'      => new sfValidatorDateTime(array('required' => false)),
      'CODIGO_BARRAS'       => new sfValidatorString(array('max_length' => 100, 'required' => false)),
      'ASUNTO'              => new sfValidatorString(array('max_length' => 300, 'required' => false)),
      'RUTA'                => new sfValidatorString(array('required' => false)),
      'NUMERO_RADICACION'   => new sfValidatorInteger(array('min' => -2147483648, 'max' => 2147483647, 'required' => false)),
      'RADICADO'            => new sfValidatorString(array('max_length' => 30, 'required' => false)),
      'MARCA'               => new sfValidatorInteger(array('min' => -2147483648, 'max' => 2147483647, 'required' => false)),
      'MARCACAUSACION'      => new sfValidatorInteger(array('min' => -2147483648, 'max' => 2147483647, 'required' => false)),
      'CAUSADAENSAP'        => new sfValidatorInteger(array('min' => -2147483648, 'max' => 2147483647, 'required' => false)),
      'FECHA_VENCIMIENTO'   => new sfValidatorDateTime(array('required' => false)),
      'TIPOFIRMADIGITAL_ID' => new sfValidatorInteger(array('min' => -2147483648, 'max' => 2147483647, 'required' => false)),
      'DOC_CAUSACION'       => new sfValidatorString(array('max_length' => 50, 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('factura[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'Factura';
  }


}
