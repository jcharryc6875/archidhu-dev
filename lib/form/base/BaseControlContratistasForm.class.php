<?php

/**
 * ControlContratistas form base class.
 *
 * @method ControlContratistas getObject() Returns the current form's model object
 *
 * @package    ##PROJECT_NAME##
 * @subpackage form
 * @author     ##AUTHOR_NAME##
 */
abstract class BaseControlContratistasForm extends BaseFormPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'CONTROLCONTRATISTAS_ID'    => new sfWidgetFormInputHidden(),
      'ESTADOCONTRATISTA_ID'      => new sfWidgetFormPropelChoice(array('model' => 'EstadoContratista', 'add_empty' => false)),
      'TIPOIDENTIFICACION_ID'     => new sfWidgetFormPropelChoice(array('model' => 'TipoIdentificacion', 'add_empty' => false)),
      'USUARIO_ID'                => new sfWidgetFormPropelChoice(array('model' => 'Usuario', 'add_empty' => false)),
      'CIUDAD_ID'                 => new sfWidgetFormPropelChoice(array('model' => 'Ciudad', 'add_empty' => false)),
      'DEPENDENCIA_ID'            => new sfWidgetFormPropelChoice(array('model' => 'Dependencia', 'add_empty' => false)),
      'CARGO_ID'                  => new sfWidgetFormPropelChoice(array('model' => 'Cargo', 'add_empty' => false)),
      'TIPOCONTRATO_ID'           => new sfWidgetFormPropelChoice(array('model' => 'TipoContrato', 'add_empty' => false)),
      'NUMERO_IDENTIFICACION'     => new sfWidgetFormInputText(),
      'NOMBRE'                    => new sfWidgetFormInputText(),
      'PRIMER_APELLIDO'           => new sfWidgetFormInputText(),
      'SEGUNDO_APELLIDO'          => new sfWidgetFormInputText(),
      'GENERO'                    => new sfWidgetFormInputText(),
      'NACIONALIDAD'              => new sfWidgetFormInputText(),
      'LIBRETA_MILITAR'           => new sfWidgetFormInputText(),
      'FECHA_NACIMIENTO'          => new sfWidgetFormDateTime(),
      'DIRECCION_ENVIO'           => new sfWidgetFormInputText(),
      'EMAIL'                     => new sfWidgetFormInputText(),
      'EDUCACION_BASICA'          => new sfWidgetFormInputText(),
      'FECHA_GRADO_BASICA'        => new sfWidgetFormDateTime(),
      'EDUCACION_SUPERIOR'        => new sfWidgetFormInputText(),
      'EXPERIENCIA_LABORAL'       => new sfWidgetFormInputText(),
      'EPS'                       => new sfWidgetFormInputText(),
      'FONDO_PENSIONES'           => new sfWidgetFormInputText(),
      'FECHA_CREACION'            => new sfWidgetFormDateTime(),
      'TITULO_UNIVERSITARIO'      => new sfWidgetFormInputText(),
      'FECHA_GRADO_UNIVERSITARIO' => new sfWidgetFormDateTime(),
      'NUMERO_TARJETA'            => new sfWidgetFormInputText(),
      'OTROS_IDIOMAS'             => new sfWidgetFormInputText(),
      'UBICACION_LABORAL'         => new sfWidgetFormInputText(),
      'ESTADO_CIVIL'              => new sfWidgetFormInputText(),
      'NUMERO_HIJOS'              => new sfWidgetFormInputText(),
    ));

    $this->setValidators(array(
      'CONTROLCONTRATISTAS_ID'    => new sfValidatorChoice(array('choices' => array($this->getObject()->getControlcontratistasId()), 'empty_value' => $this->getObject()->getControlcontratistasId(), 'required' => false)),
      'ESTADOCONTRATISTA_ID'      => new sfValidatorPropelChoice(array('model' => 'EstadoContratista', 'column' => 'ESTADOCONTRATISTA_ID')),
      'TIPOIDENTIFICACION_ID'     => new sfValidatorPropelChoice(array('model' => 'TipoIdentificacion', 'column' => 'TIPOIDENTIFICACION_ID')),
      'USUARIO_ID'                => new sfValidatorPropelChoice(array('model' => 'Usuario', 'column' => 'USUARIO_ID')),
      'CIUDAD_ID'                 => new sfValidatorPropelChoice(array('model' => 'Ciudad', 'column' => 'CIUDAD_ID')),
      'DEPENDENCIA_ID'            => new sfValidatorPropelChoice(array('model' => 'Dependencia', 'column' => 'DEPENDENCIA_ID')),
      'CARGO_ID'                  => new sfValidatorPropelChoice(array('model' => 'Cargo', 'column' => 'CARGO_ID')),
      'TIPOCONTRATO_ID'           => new sfValidatorPropelChoice(array('model' => 'TipoContrato', 'column' => 'TIPOCONTRATO_ID')),
      'NUMERO_IDENTIFICACION'     => new sfValidatorString(array('max_length' => 100)),
      'NOMBRE'                    => new sfValidatorString(array('max_length' => 250)),
      'PRIMER_APELLIDO'           => new sfValidatorString(array('max_length' => 50)),
      'SEGUNDO_APELLIDO'          => new sfValidatorString(array('max_length' => 50, 'required' => false)),
      'GENERO'                    => new sfValidatorString(array('max_length' => 50, 'required' => false)),
      'NACIONALIDAD'              => new sfValidatorString(array('max_length' => 50, 'required' => false)),
      'LIBRETA_MILITAR'           => new sfValidatorString(array('max_length' => 300, 'required' => false)),
      'FECHA_NACIMIENTO'          => new sfValidatorDateTime(array('required' => false)),
      'DIRECCION_ENVIO'           => new sfValidatorString(array('max_length' => 500, 'required' => false)),
      'EMAIL'                     => new sfValidatorString(array('max_length' => 50, 'required' => false)),
      'EDUCACION_BASICA'          => new sfValidatorString(array('max_length' => 500, 'required' => false)),
      'FECHA_GRADO_BASICA'        => new sfValidatorDateTime(array('required' => false)),
      'EDUCACION_SUPERIOR'        => new sfValidatorString(array('max_length' => 500, 'required' => false)),
      'EXPERIENCIA_LABORAL'       => new sfValidatorString(array('max_length' => 200, 'required' => false)),
      'EPS'                       => new sfValidatorString(array('max_length' => 150, 'required' => false)),
      'FONDO_PENSIONES'           => new sfValidatorString(array('max_length' => 150, 'required' => false)),
      'FECHA_CREACION'            => new sfValidatorDateTime(array('required' => false)),
      'TITULO_UNIVERSITARIO'      => new sfValidatorString(array('max_length' => 200, 'required' => false)),
      'FECHA_GRADO_UNIVERSITARIO' => new sfValidatorDateTime(array('required' => false)),
      'NUMERO_TARJETA'            => new sfValidatorString(array('max_length' => 50, 'required' => false)),
      'OTROS_IDIOMAS'             => new sfValidatorString(array('max_length' => 50, 'required' => false)),
      'UBICACION_LABORAL'         => new sfValidatorString(array('max_length' => 50, 'required' => false)),
      'ESTADO_CIVIL'              => new sfValidatorString(array('max_length' => 50, 'required' => false)),
      'NUMERO_HIJOS'              => new sfValidatorInteger(array('min' => -2147483648, 'max' => 2147483647, 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('control_contratistas[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'ControlContratistas';
  }


}
