<?php

/**
 * Proveedor form base class.
 *
 * @method Proveedor getObject() Returns the current form's model object
 *
 * @package    ##PROJECT_NAME##
 * @subpackage form
 * @author     ##AUTHOR_NAME##
 */
abstract class BaseProveedorForm extends BaseFormPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'PROVEEDOR_ID'        => new sfWidgetFormInputHidden(),
      'NOMBRE'              => new sfWidgetFormInputText(),
      'NIT'                 => new sfWidgetFormInputText(),
      'REPRESENTANTE_LEGAL' => new sfWidgetFormInputText(),
      'IDENTIFICACION_RL'   => new sfWidgetFormInputText(),
      'DIRECCION'           => new sfWidgetFormInputText(),
      'TELEFONO'            => new sfWidgetFormInputText(),
      'NATURALEZA_JURIDICA' => new sfWidgetFormInputText(),
      'PAIS_ID'             => new sfWidgetFormPropelChoice(array('model' => 'Pais', 'add_empty' => true)),
      'PROV_ESTADO_ID'      => new sfWidgetFormPropelChoice(array('model' => 'ProvEstado', 'add_empty' => true)),
      'NOMBRE_COMERCIAL'    => new sfWidgetFormInputText(),
      'FECHA_INGRESO'       => new sfWidgetFormDateTime(),
      'FECHA_CREACION'      => new sfWidgetFormDateTime(),
    ));

    $this->setValidators(array(
      'PROVEEDOR_ID'        => new sfValidatorChoice(array('choices' => array($this->getObject()->getProveedorId()), 'empty_value' => $this->getObject()->getProveedorId(), 'required' => false)),
      'NOMBRE'              => new sfValidatorString(array('max_length' => 100, 'required' => false)),
      'NIT'                 => new sfValidatorString(array('max_length' => 30, 'required' => false)),
      'REPRESENTANTE_LEGAL' => new sfValidatorString(array('max_length' => 100, 'required' => false)),
      'IDENTIFICACION_RL'   => new sfValidatorString(array('max_length' => 50, 'required' => false)),
      'DIRECCION'           => new sfValidatorString(array('max_length' => 200, 'required' => false)),
      'TELEFONO'            => new sfValidatorString(array('max_length' => 80, 'required' => false)),
      'NATURALEZA_JURIDICA' => new sfValidatorString(array('max_length' => 80, 'required' => false)),
      'PAIS_ID'             => new sfValidatorPropelChoice(array('model' => 'Pais', 'column' => 'PAIS_ID', 'required' => false)),
      'PROV_ESTADO_ID'      => new sfValidatorPropelChoice(array('model' => 'ProvEstado', 'column' => 'PROV_ESTADO_ID', 'required' => false)),
      'NOMBRE_COMERCIAL'    => new sfValidatorString(array('max_length' => 200, 'required' => false)),
      'FECHA_INGRESO'       => new sfValidatorDateTime(array('required' => false)),
      'FECHA_CREACION'      => new sfValidatorDateTime(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('proveedor[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'Proveedor';
  }


}
