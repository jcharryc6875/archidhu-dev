<?php

/**
 * FacturaVitacora form base class.
 *
 * @method FacturaVitacora getObject() Returns the current form's model object
 *
 * @package    ##PROJECT_NAME##
 * @subpackage form
 * @author     ##AUTHOR_NAME##
 */
abstract class BaseFacturaVitacoraForm extends BaseFormPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'FACTURAVITACORA_ID'       => new sfWidgetFormInputHidden(),
      'FACTURAPROCESO_ID'        => new sfWidgetFormPropelChoice(array('model' => 'FacturaProceso', 'add_empty' => false)),
      'FACTURAESTADO_ID'         => new sfWidgetFormPropelChoice(array('model' => 'FacturaEstado', 'add_empty' => false)),
      'FACTURA_ID'               => new sfWidgetFormPropelChoice(array('model' => 'Factura', 'add_empty' => false)),
      'FACTURAVITACORAESTADO_ID' => new sfWidgetFormPropelChoice(array('model' => 'FacturaVitacoraEstado', 'add_empty' => true)),
      'FECHA_I'                  => new sfWidgetFormDateTime(),
      'FECHA_I_DATE'             => new sfWidgetFormDateTime(),
      'FECHA_F'                  => new sfWidgetFormDateTime(),
      'FECHA_F_DATE'             => new sfWidgetFormDateTime(),
      'OBSERVACIONES'            => new sfWidgetFormInputText(),
      'EJECUTADA'                => new sfWidgetFormInputText(),
    ));

    $this->setValidators(array(
      'FACTURAVITACORA_ID'       => new sfValidatorChoice(array('choices' => array($this->getObject()->getFacturavitacoraId()), 'empty_value' => $this->getObject()->getFacturavitacoraId(), 'required' => false)),
      'FACTURAPROCESO_ID'        => new sfValidatorPropelChoice(array('model' => 'FacturaProceso', 'column' => 'FACTURAPROCESO_ID')),
      'FACTURAESTADO_ID'         => new sfValidatorPropelChoice(array('model' => 'FacturaEstado', 'column' => 'FACTURAESTADO_ID')),
      'FACTURA_ID'               => new sfValidatorPropelChoice(array('model' => 'Factura', 'column' => 'FACTURA_ID')),
      'FACTURAVITACORAESTADO_ID' => new sfValidatorPropelChoice(array('model' => 'FacturaVitacoraEstado', 'column' => 'FACTURAVITACORAESTADO_ID', 'required' => false)),
      'FECHA_I'                  => new sfValidatorDateTime(array('required' => false)),
      'FECHA_I_DATE'             => new sfValidatorDateTime(array('required' => false)),
      'FECHA_F'                  => new sfValidatorDateTime(array('required' => false)),
      'FECHA_F_DATE'             => new sfValidatorDateTime(array('required' => false)),
      'OBSERVACIONES'            => new sfValidatorString(array('max_length' => 4000, 'required' => false)),
      'EJECUTADA'                => new sfValidatorInteger(array('min' => -128, 'max' => 127, 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('factura_vitacora[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'FacturaVitacora';
  }


}
