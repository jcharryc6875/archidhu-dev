<?php

/**
 * Cliente form base class.
 *
 * @method Cliente getObject() Returns the current form's model object
 *
 * @package    ##PROJECT_NAME##
 * @subpackage form
 * @author     ##AUTHOR_NAME##
 */
abstract class BaseClienteForm extends BaseFormPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'CLIENTE_ID'             => new sfWidgetFormInputHidden(),
      'FRECCONSULTACLIENTE_ID' => new sfWidgetFormPropelChoice(array('model' => 'FrecConsultaCliente', 'add_empty' => false)),
      'SUBSERIE_ID'            => new sfWidgetFormInputText(),
      'CLIENTEESTADO_ID'       => new sfWidgetFormPropelChoice(array('model' => 'ClienteEstado', 'add_empty' => false)),
      'UNIDCONSERVADORA_ID'    => new sfWidgetFormPropelChoice(array('model' => 'Unidconservadora', 'add_empty' => false)),
      'SOPORTE_CLIENTE_ID'     => new sfWidgetFormPropelChoice(array('model' => 'SoporteCliente', 'add_empty' => false)),
      'CODIGO_CLIENTE'         => new sfWidgetFormInputText(),
      'FECHA_APERTURA'         => new sfWidgetFormDateTime(),
      'FECHA_CIERRE'           => new sfWidgetFormDateTime(),
      'NOMBRE_CLIENTE'         => new sfWidgetFormInputText(),
      'CONTENIDO'              => new sfWidgetFormInputText(),
      'CREADO_POR_WEB'         => new sfWidgetFormInputText(),
      'UBICACION'              => new sfWidgetFormInputText(),
      'FOLIOS'                 => new sfWidgetFormInputText(),
      'FECHA_CREACION'         => new sfWidgetFormDateTime(),
      'NOTAS'                  => new sfWidgetFormInputText(),
      'FECHA_VENCIMIENTO'      => new sfWidgetFormDateTime(),
      'VOLUMEN'                => new sfWidgetFormInputText(),
      'FECHA_AFILIACION'       => new sfWidgetFormDateTime(),
      'MARCA'                  => new sfWidgetFormInputText(),
    ));

    $this->setValidators(array(
      'CLIENTE_ID'             => new sfValidatorChoice(array('choices' => array($this->getObject()->getClienteId()), 'empty_value' => $this->getObject()->getClienteId(), 'required' => false)),
      'FRECCONSULTACLIENTE_ID' => new sfValidatorPropelChoice(array('model' => 'FrecConsultaCliente', 'column' => 'FRECCONSULTACLIENTE_ID')),
      'SUBSERIE_ID'            => new sfValidatorInteger(array('min' => -2147483648, 'max' => 2147483647, 'required' => false)),
      'CLIENTEESTADO_ID'       => new sfValidatorPropelChoice(array('model' => 'ClienteEstado', 'column' => 'CLIENTEESTADO_ID')),
      'UNIDCONSERVADORA_ID'    => new sfValidatorPropelChoice(array('model' => 'Unidconservadora', 'column' => 'UNIDCONSERVADORA_ID')),
      'SOPORTE_CLIENTE_ID'     => new sfValidatorPropelChoice(array('model' => 'SoporteCliente', 'column' => 'SOPORTE_CLIENTE_ID')),
      'CODIGO_CLIENTE'         => new sfValidatorString(array('max_length' => 30, 'required' => false)),
      'FECHA_APERTURA'         => new sfValidatorDateTime(array('required' => false)),
      'FECHA_CIERRE'           => new sfValidatorDateTime(array('required' => false)),
      'NOMBRE_CLIENTE'         => new sfValidatorString(array('max_length' => 200, 'required' => false)),
      'CONTENIDO'              => new sfValidatorString(array('max_length' => 1000, 'required' => false)),
      'CREADO_POR_WEB'         => new sfValidatorInteger(array('min' => -128, 'max' => 127, 'required' => false)),
      'UBICACION'              => new sfValidatorString(array('max_length' => 30, 'required' => false)),
      'FOLIOS'                 => new sfValidatorInteger(array('min' => -2147483648, 'max' => 2147483647, 'required' => false)),
      'FECHA_CREACION'         => new sfValidatorDateTime(array('required' => false)),
      'NOTAS'                  => new sfValidatorString(array('max_length' => 300, 'required' => false)),
      'FECHA_VENCIMIENTO'      => new sfValidatorDateTime(array('required' => false)),
      'VOLUMEN'                => new sfValidatorNumber(array('required' => false)),
      'FECHA_AFILIACION'       => new sfValidatorDateTime(array('required' => false)),
      'MARCA'                  => new sfValidatorInteger(array('min' => -2147483648, 'max' => 2147483647, 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('cliente[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'Cliente';
  }


}
