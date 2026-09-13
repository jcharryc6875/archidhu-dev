<?php

/**
 * Usuario form base class.
 *
 * @method Usuario getObject() Returns the current form's model object
 *
 * @package    ##PROJECT_NAME##
 * @subpackage form
 * @author     ##AUTHOR_NAME##
 */
abstract class BaseUsuarioForm extends BaseFormPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'USUARIO_ID'            => new sfWidgetFormInputHidden(),
      'CARGO_ID'              => new sfWidgetFormPropelChoice(array('model' => 'Cargo', 'add_empty' => true)),
      'ESTADOUSUARIO_ID'      => new sfWidgetFormPropelChoice(array('model' => 'EstadoUsuario', 'add_empty' => false)),
      'DEPENDENCIA_ID'        => new sfWidgetFormPropelChoice(array('model' => 'Dependencia', 'add_empty' => false)),
      'REGIONAL_ID'           => new sfWidgetFormPropelChoice(array('model' => 'Regional', 'add_empty' => false)),
      'USER_NAME'             => new sfWidgetFormInputText(),
      'PASSWORD'              => new sfWidgetFormInputText(),
      'NOMBRE'                => new sfWidgetFormInputText(),
      'APELLIDO'              => new sfWidgetFormInputText(),
      'CEDULA'                => new sfWidgetFormInputText(),
      'EMAIL'                 => new sfWidgetFormInputText(),
      'INICIALES'             => new sfWidgetFormInputText(),
      'EXTENSION'             => new sfWidgetFormInputText(),
      'INTENTOS'              => new sfWidgetFormInputText(),
      'PREFIJO'               => new sfWidgetFormInputText(),
      'FECHA_CREACION'        => new sfWidgetFormDateTime(),
      'RUTA_FOTO'             => new sfWidgetFormInputText(),
      'SALT'                  => new sfWidgetFormInputText(),
      'FECHA_ACTUALIZACION'   => new sfWidgetFormDateTime(),
      'USUARIO_AD'            => new sfWidgetFormInputText(),
      'FIRMA_ELECTRONICA'     => new sfWidgetFormInputText(),
      'USE_FIRMA_ELECTRONICA' => new sfWidgetFormInputText(),
    ));

    $this->setValidators(array(
      'USUARIO_ID'            => new sfValidatorChoice(array('choices' => array($this->getObject()->getUsuarioId()), 'empty_value' => $this->getObject()->getUsuarioId(), 'required' => false)),
      'CARGO_ID'              => new sfValidatorPropelChoice(array('model' => 'Cargo', 'column' => 'CARGO_ID', 'required' => false)),
      'ESTADOUSUARIO_ID'      => new sfValidatorPropelChoice(array('model' => 'EstadoUsuario', 'column' => 'ESTADOUSUARIO_ID')),
      'DEPENDENCIA_ID'        => new sfValidatorPropelChoice(array('model' => 'Dependencia', 'column' => 'DEPENDENCIA_ID')),
      'REGIONAL_ID'           => new sfValidatorPropelChoice(array('model' => 'Regional', 'column' => 'REGIONAL_ID')),
      'USER_NAME'             => new sfValidatorString(array('max_length' => 50)),
      'PASSWORD'              => new sfValidatorString(array('max_length' => 100, 'required' => false)),
      'NOMBRE'                => new sfValidatorString(array('max_length' => 100, 'required' => false)),
      'APELLIDO'              => new sfValidatorString(array('max_length' => 100, 'required' => false)),
      'CEDULA'                => new sfValidatorString(array('max_length' => 20, 'required' => false)),
      'EMAIL'                 => new sfValidatorString(array('max_length' => 100, 'required' => false)),
      'INICIALES'             => new sfValidatorString(array('max_length' => 20, 'required' => false)),
      'EXTENSION'             => new sfValidatorString(array('max_length' => 10, 'required' => false)),
      'INTENTOS'              => new sfValidatorInteger(array('min' => -2147483648, 'max' => 2147483647, 'required' => false)),
      'PREFIJO'               => new sfValidatorString(array('max_length' => 20, 'required' => false)),
      'FECHA_CREACION'        => new sfValidatorDateTime(array('required' => false)),
      'RUTA_FOTO'             => new sfValidatorString(array('max_length' => 500, 'required' => false)),
      'SALT'                  => new sfValidatorString(array('max_length' => 100, 'required' => false)),
      'FECHA_ACTUALIZACION'   => new sfValidatorDateTime(array('required' => false)),
      'USUARIO_AD'            => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'FIRMA_ELECTRONICA'     => new sfValidatorString(array('max_length' => 800, 'required' => false)),
      'USE_FIRMA_ELECTRONICA' => new sfValidatorInteger(array('min' => -128, 'max' => 127, 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('usuario[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'Usuario';
  }


}
