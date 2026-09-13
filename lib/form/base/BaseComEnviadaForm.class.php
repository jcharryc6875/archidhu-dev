<?php

/**
 * ComEnviada form base class.
 *
 * @method ComEnviada getObject() Returns the current form's model object
 *
 * @package    ##PROJECT_NAME##
 * @subpackage form
 * @author     ##AUTHOR_NAME##
 */
abstract class BaseComEnviadaForm extends BaseFormPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'COMENVIADA_ID'           => new sfWidgetFormInputHidden(),
      'CIUDAD_ID'               => new sfWidgetFormPropelChoice(array('model' => 'Ciudad', 'add_empty' => false)),
      'DEPENDENCIA_ID'          => new sfWidgetFormPropelChoice(array('model' => 'Dependencia', 'add_empty' => false)),
      'ESTADOCOMENVIADA_ID'     => new sfWidgetFormPropelChoice(array('model' => 'EstadoComEnviada', 'add_empty' => false)),
      'EMPRESA_MENSAJERIA_ID'   => new sfWidgetFormPropelChoice(array('model' => 'EmpresaMensajeria', 'add_empty' => true)),
      'REGIONAL_ID'             => new sfWidgetFormPropelChoice(array('model' => 'Regional', 'add_empty' => false)),
      'PERIODO_ID'              => new sfWidgetFormPropelChoice(array('model' => 'Periodo', 'add_empty' => false)),
      'ESTADODIGITALIZACION_ID' => new sfWidgetFormPropelChoice(array('model' => 'EstadoDigitalizacion', 'add_empty' => false)),
      'NUMERO_RADICACION'       => new sfWidgetFormInputText(),
      'RADICADO'                => new sfWidgetFormInputText(),
      'ASUNTO'                  => new sfWidgetFormInputText(),
      'FECHA_CREACION'          => new sfWidgetFormDateTime(),
      'GUIA'                    => new sfWidgetFormInputText(),
      'VALOR_GUIA'              => new sfWidgetFormInputText(),
      'FECHA_ENVIO_GUIA'        => new sfWidgetFormDateTime(),
      'ES_COPIA'                => new sfWidgetFormInputText(),
      'FOLIOS'                  => new sfWidgetFormInputText(),
      'ANEXOS'                  => new sfWidgetFormInputText(),
      'ESTAENTREGADO'           => new sfWidgetFormInputText(),
      'CONTENIDO'               => new sfWidgetFormTextarea(),
      'FECHA_DE_ANULACION'      => new sfWidgetFormDateTime(),
      'OBS_ANULACION'           => new sfWidgetFormInputText(),
      'RUTA'                    => new sfWidgetFormInputText(),
      'MARCA'                   => new sfWidgetFormInputText(),
      'INSTANCIA'               => new sfWidgetFormInputText(),
      'BITACORA'                => new sfWidgetFormInputText(),
      'banco_id'                => new sfWidgetFormInputText(),
      'banco_radicado'          => new sfWidgetFormTextarea(),
      'banco_numero_radicado'   => new sfWidgetFormInputText(),
      'FECHA_PRORROGRA'         => new sfWidgetFormDateTime(),
      'FUNCIONARIO_DESTINO'     => new sfWidgetFormInputText(),
      'MARCA_VINCULACION'       => new sfWidgetFormInputText(),
      'PREFIJO'                 => new sfWidgetFormInputText(),
      'CARGO_DESTINATARIO'      => new sfWidgetFormInputText(),
      'DIRECCION_DESTINATARIO'  => new sfWidgetFormInputText(),
      'OBSERVACIONES_ENVIO'     => new sfWidgetFormInputText(),
      'IDIOMA_ID'               => new sfWidgetFormPropelChoice(array('model' => 'Idioma', 'add_empty' => true)),
      'PLANTILLASCOM_ID'        => new sfWidgetFormPropelChoice(array('model' => 'PlantillasCom', 'add_empty' => true)),
      'CONSECUTIVO'             => new sfWidgetFormInputText(),
      'URL_FILE_WORD'           => new sfWidgetFormInputText(),
      'IS_CREATE_WORD'          => new sfWidgetFormInputText(),
      'TIPOFIRMADIGITAL_ID'     => new sfWidgetFormInputText(),
      'USE_MEMBRETE'            => new sfWidgetFormInputText(),
      'CONSECUTIVO_RESP'        => new sfWidgetFormInputText(),
      'FIRMA_ELECTRONICA'       => new sfWidgetFormInputText(),
    ));

    $this->setValidators(array(
      'COMENVIADA_ID'           => new sfValidatorChoice(array('choices' => array($this->getObject()->getComenviadaId()), 'empty_value' => $this->getObject()->getComenviadaId(), 'required' => false)),
      'CIUDAD_ID'               => new sfValidatorPropelChoice(array('model' => 'Ciudad', 'column' => 'CIUDAD_ID')),
      'DEPENDENCIA_ID'          => new sfValidatorPropelChoice(array('model' => 'Dependencia', 'column' => 'DEPENDENCIA_ID')),
      'ESTADOCOMENVIADA_ID'     => new sfValidatorPropelChoice(array('model' => 'EstadoComEnviada', 'column' => 'ESTADOCOMENVIADA_ID')),
      'EMPRESA_MENSAJERIA_ID'   => new sfValidatorPropelChoice(array('model' => 'EmpresaMensajeria', 'column' => 'EMPRESA_MENSAJERIA_ID', 'required' => false)),
      'REGIONAL_ID'             => new sfValidatorPropelChoice(array('model' => 'Regional', 'column' => 'REGIONAL_ID')),
      'PERIODO_ID'              => new sfValidatorPropelChoice(array('model' => 'Periodo', 'column' => 'PERIODO_ID')),
      'ESTADODIGITALIZACION_ID' => new sfValidatorPropelChoice(array('model' => 'EstadoDigitalizacion', 'column' => 'ESTADODIGITALIZACION_ID')),
      'NUMERO_RADICACION'       => new sfValidatorInteger(array('min' => -2147483648, 'max' => 2147483647, 'required' => false)),
      'RADICADO'                => new sfValidatorString(array('max_length' => 50, 'required' => false)),
      'ASUNTO'                  => new sfValidatorString(array('max_length' => 500, 'required' => false)),
      'FECHA_CREACION'          => new sfValidatorDateTime(array('required' => false)),
      'GUIA'                    => new sfValidatorString(array('max_length' => 50, 'required' => false)),
      'VALOR_GUIA'              => new sfValidatorNumber(array('required' => false)),
      'FECHA_ENVIO_GUIA'        => new sfValidatorDateTime(array('required' => false)),
      'ES_COPIA'                => new sfValidatorInteger(array('min' => -128, 'max' => 127, 'required' => false)),
      'FOLIOS'                  => new sfValidatorInteger(array('min' => -2147483648, 'max' => 2147483647, 'required' => false)),
      'ANEXOS'                  => new sfValidatorString(array('max_length' => 200, 'required' => false)),
      'ESTAENTREGADO'           => new sfValidatorInteger(array('min' => -128, 'max' => 127, 'required' => false)),
      'CONTENIDO'               => new sfValidatorString(array('required' => false)),
      'FECHA_DE_ANULACION'      => new sfValidatorDateTime(array('required' => false)),
      'OBS_ANULACION'           => new sfValidatorString(array('max_length' => 500, 'required' => false)),
      'RUTA'                    => new sfValidatorString(array('max_length' => 900, 'required' => false)),
      'MARCA'                   => new sfValidatorInteger(array('min' => -2147483648, 'max' => 2147483647, 'required' => false)),
      'INSTANCIA'               => new sfValidatorInteger(array('min' => -2147483648, 'max' => 2147483647, 'required' => false)),
      'BITACORA'                => new sfValidatorInteger(array('min' => -2147483648, 'max' => 2147483647, 'required' => false)),
      'banco_id'                => new sfValidatorInteger(array('min' => -2147483648, 'max' => 2147483647, 'required' => false)),
      'banco_radicado'          => new sfValidatorString(array('required' => false)),
      'banco_numero_radicado'   => new sfValidatorInteger(array('min' => -2147483648, 'max' => 2147483647, 'required' => false)),
      'FECHA_PRORROGRA'         => new sfValidatorDateTime(array('required' => false)),
      'FUNCIONARIO_DESTINO'     => new sfValidatorString(array('max_length' => 200, 'required' => false)),
      'MARCA_VINCULACION'       => new sfValidatorInteger(array('min' => -128, 'max' => 127, 'required' => false)),
      'PREFIJO'                 => new sfValidatorString(array('max_length' => 20, 'required' => false)),
      'CARGO_DESTINATARIO'      => new sfValidatorString(array('max_length' => 50, 'required' => false)),
      'DIRECCION_DESTINATARIO'  => new sfValidatorString(array('max_length' => 80, 'required' => false)),
      'OBSERVACIONES_ENVIO'     => new sfValidatorString(array('max_length' => 50, 'required' => false)),
      'IDIOMA_ID'               => new sfValidatorPropelChoice(array('model' => 'Idioma', 'column' => 'IDIOMA_ID', 'required' => false)),
      'PLANTILLASCOM_ID'        => new sfValidatorPropelChoice(array('model' => 'PlantillasCom', 'column' => 'PLANTILLASCOM_ID', 'required' => false)),
      'CONSECUTIVO'             => new sfValidatorInteger(array('min' => -2147483648, 'max' => 2147483647, 'required' => false)),
      'URL_FILE_WORD'           => new sfValidatorString(array('max_length' => 200, 'required' => false)),
      'IS_CREATE_WORD'          => new sfValidatorInteger(array('min' => -128, 'max' => 127, 'required' => false)),
      'TIPOFIRMADIGITAL_ID'     => new sfValidatorInteger(array('min' => -2147483648, 'max' => 2147483647, 'required' => false)),
      'USE_MEMBRETE'            => new sfValidatorInteger(array('min' => -2147483648, 'max' => 2147483647, 'required' => false)),
      'CONSECUTIVO_RESP'        => new sfValidatorInteger(array('min' => -2147483648, 'max' => 2147483647, 'required' => false)),
      'FIRMA_ELECTRONICA'       => new sfValidatorInteger(array('min' => -2147483648, 'max' => 2147483647, 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('com_enviada[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'ComEnviada';
  }


}
