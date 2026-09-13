<?php

/**
 * ComRecibida form base class.
 *
 * @method ComRecibida getObject() Returns the current form's model object
 *
 * @package    ##PROJECT_NAME##
 * @subpackage form
 * @author     ##AUTHOR_NAME##
 */
abstract class BaseComRecibidaForm extends BaseFormPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'COMRECIBIDA_ID'             => new sfWidgetFormInputHidden(),
      'REGIONAL_ID'                => new sfWidgetFormPropelChoice(array('model' => 'Regional', 'add_empty' => false)),
      'COMENVIADA_ID'              => new sfWidgetFormPropelChoice(array('model' => 'ComEnviada', 'add_empty' => true)),
      'ASUNTORECIBIDA_ID'          => new sfWidgetFormPropelChoice(array('model' => 'AsuntoRecibida', 'add_empty' => false)),
      'TIPOCOMRECIBIDA_ID'         => new sfWidgetFormPropelChoice(array('model' => 'TipoComRecibida', 'add_empty' => false)),
      'PERIODO_ID'                 => new sfWidgetFormPropelChoice(array('model' => 'Periodo', 'add_empty' => false)),
      'DEPENDENCIA_ID'             => new sfWidgetFormPropelChoice(array('model' => 'Dependencia', 'add_empty' => false)),
      'ESTADODIGITALIZACION_ID'    => new sfWidgetFormPropelChoice(array('model' => 'EstadoDigitalizacion', 'add_empty' => false)),
      'ESTADOCOMRECIBIDA_ID'       => new sfWidgetFormPropelChoice(array('model' => 'EstadoComRecibida', 'add_empty' => false)),
      'FORMARECEPCION_ID'          => new sfWidgetFormPropelChoice(array('model' => 'FormaRecepcion', 'add_empty' => false)),
      'CIUDAD_ID'                  => new sfWidgetFormPropelChoice(array('model' => 'Ciudad', 'add_empty' => false)),
      'DIRECTORIOEXTERNO_ID'       => new sfWidgetFormPropelChoice(array('model' => 'DirectorioExterno', 'add_empty' => false)),
      'EMPRESA_MENSAJERIA_ID'      => new sfWidgetFormPropelChoice(array('model' => 'EmpresaMensajeria', 'add_empty' => true)),
      'TIPOIMPUESTO_ID'            => new sfWidgetFormPropelChoice(array('model' => 'TipoImpuesto', 'add_empty' => true)),
      'TIPORESPUESTA_ID'           => new sfWidgetFormPropelChoice(array('model' => 'TipoRespuesta', 'add_empty' => true)),
      'MEDIORESPUESTA_ID'          => new sfWidgetFormPropelChoice(array('model' => 'MedioRespuesta', 'add_empty' => true)),
      'RADICADO_ORIGEN'            => new sfWidgetFormInputText(),
      'FECHA_CREACION'             => new sfWidgetFormDateTime(),
      'ASUNTO'                     => new sfWidgetFormInputText(),
      'NUMERO_RADICACION'          => new sfWidgetFormInputText(),
      'RADICADO'                   => new sfWidgetFormInputText(),
      'ES_COPIA'                   => new sfWidgetFormInputText(),
      'GUIA'                       => new sfWidgetFormInputText(),
      'FECHA_ENVIO_GUIA'           => new sfWidgetFormDateTime(),
      'VALOR_GUIA'                 => new sfWidgetFormInputText(),
      'FOLIOS'                     => new sfWidgetFormInputText(),
      'ANEXOS'                     => new sfWidgetFormInputText(),
      'RUTA'                       => new sfWidgetFormInputText(),
      'CODIGO_REEN_RESP'           => new sfWidgetFormInputText(),
      'OBS_REEN_RESP'              => new sfWidgetFormInputText(),
      'ESTAENTREGADO'              => new sfWidgetFormInputText(),
      'FECHA_MAXIMA_RESPUESTA'     => new sfWidgetFormDateTime(),
      'FECHA_DE_ANULACION'         => new sfWidgetFormDateTime(),
      'OBS_ANULACION'              => new sfWidgetFormInputText(),
      'MARCA'                      => new sfWidgetFormInputText(),
      'FECHA_RESPUESTA_REEN'       => new sfWidgetFormDateTime(),
      'OBSERVACIONES'              => new sfWidgetFormInputText(),
      'INSTANCIA'                  => new sfWidgetFormInputText(),
      'BITACORA'                   => new sfWidgetFormInputText(),
      'OBS_REMITIR'                => new sfWidgetFormInputText(),
      'DESTINO_COMRECIBIDA'        => new sfWidgetFormInputText(),
      'FECHA_MAXIMA_RESPUESTA_IMP' => new sfWidgetFormDateTime(),
      'FECHA_DOCUMENTO'            => new sfWidgetFormDateTime(),
      'VALOR_FACTURA'              => new sfWidgetFormInputText(),
      'VALOR_FACTURA_US'           => new sfWidgetFormInputText(),
      'MARCA_VINCULACION'          => new sfWidgetFormInputText(),
      'FECHA_PAGO_FACTURA'         => new sfWidgetFormDateTime(),
      'TIPOFIRMADIGITAL_ID'        => new sfWidgetFormInputText(),
    ));

    $this->setValidators(array(
      'COMRECIBIDA_ID'             => new sfValidatorChoice(array('choices' => array($this->getObject()->getComrecibidaId()), 'empty_value' => $this->getObject()->getComrecibidaId(), 'required' => false)),
      'REGIONAL_ID'                => new sfValidatorPropelChoice(array('model' => 'Regional', 'column' => 'REGIONAL_ID')),
      'COMENVIADA_ID'              => new sfValidatorPropelChoice(array('model' => 'ComEnviada', 'column' => 'COMENVIADA_ID', 'required' => false)),
      'ASUNTORECIBIDA_ID'          => new sfValidatorPropelChoice(array('model' => 'AsuntoRecibida', 'column' => 'ASUNTORECIBIDA_ID')),
      'TIPOCOMRECIBIDA_ID'         => new sfValidatorPropelChoice(array('model' => 'TipoComRecibida', 'column' => 'TIPOCOMRECIBIDA_ID')),
      'PERIODO_ID'                 => new sfValidatorPropelChoice(array('model' => 'Periodo', 'column' => 'PERIODO_ID')),
      'DEPENDENCIA_ID'             => new sfValidatorPropelChoice(array('model' => 'Dependencia', 'column' => 'DEPENDENCIA_ID')),
      'ESTADODIGITALIZACION_ID'    => new sfValidatorPropelChoice(array('model' => 'EstadoDigitalizacion', 'column' => 'ESTADODIGITALIZACION_ID')),
      'ESTADOCOMRECIBIDA_ID'       => new sfValidatorPropelChoice(array('model' => 'EstadoComRecibida', 'column' => 'ESTADOCOMRECIBIDA_ID')),
      'FORMARECEPCION_ID'          => new sfValidatorPropelChoice(array('model' => 'FormaRecepcion', 'column' => 'FORMARECEPCION_ID')),
      'CIUDAD_ID'                  => new sfValidatorPropelChoice(array('model' => 'Ciudad', 'column' => 'CIUDAD_ID')),
      'DIRECTORIOEXTERNO_ID'       => new sfValidatorPropelChoice(array('model' => 'DirectorioExterno', 'column' => 'DIRECTORIOEXTERNO_ID')),
      'EMPRESA_MENSAJERIA_ID'      => new sfValidatorPropelChoice(array('model' => 'EmpresaMensajeria', 'column' => 'EMPRESA_MENSAJERIA_ID', 'required' => false)),
      'TIPOIMPUESTO_ID'            => new sfValidatorPropelChoice(array('model' => 'TipoImpuesto', 'column' => 'TIPOIMPUESTO_ID', 'required' => false)),
      'TIPORESPUESTA_ID'           => new sfValidatorPropelChoice(array('model' => 'TipoRespuesta', 'column' => 'TIPORESPUESTA_ID', 'required' => false)),
      'MEDIORESPUESTA_ID'          => new sfValidatorPropelChoice(array('model' => 'MedioRespuesta', 'column' => 'MEDIORESPUESTA_ID', 'required' => false)),
      'RADICADO_ORIGEN'            => new sfValidatorString(array('max_length' => 30, 'required' => false)),
      'FECHA_CREACION'             => new sfValidatorDateTime(array('required' => false)),
      'ASUNTO'                     => new sfValidatorString(array('max_length' => 300, 'required' => false)),
      'NUMERO_RADICACION'          => new sfValidatorInteger(array('min' => -2147483648, 'max' => 2147483647, 'required' => false)),
      'RADICADO'                   => new sfValidatorString(array('max_length' => 30, 'required' => false)),
      'ES_COPIA'                   => new sfValidatorInteger(array('min' => -128, 'max' => 127, 'required' => false)),
      'GUIA'                       => new sfValidatorString(array('max_length' => 30, 'required' => false)),
      'FECHA_ENVIO_GUIA'           => new sfValidatorDateTime(array('required' => false)),
      'VALOR_GUIA'                 => new sfValidatorNumber(array('required' => false)),
      'FOLIOS'                     => new sfValidatorInteger(array('min' => -2147483648, 'max' => 2147483647, 'required' => false)),
      'ANEXOS'                     => new sfValidatorString(array('max_length' => 300, 'required' => false)),
      'RUTA'                       => new sfValidatorString(array('max_length' => 1000, 'required' => false)),
      'CODIGO_REEN_RESP'           => new sfValidatorInteger(array('min' => -2147483648, 'max' => 2147483647, 'required' => false)),
      'OBS_REEN_RESP'              => new sfValidatorString(array('max_length' => 300, 'required' => false)),
      'ESTAENTREGADO'              => new sfValidatorInteger(array('min' => -128, 'max' => 127, 'required' => false)),
      'FECHA_MAXIMA_RESPUESTA'     => new sfValidatorDateTime(array('required' => false)),
      'FECHA_DE_ANULACION'         => new sfValidatorDateTime(array('required' => false)),
      'OBS_ANULACION'              => new sfValidatorString(array('max_length' => 300, 'required' => false)),
      'MARCA'                      => new sfValidatorInteger(array('min' => -2147483648, 'max' => 2147483647, 'required' => false)),
      'FECHA_RESPUESTA_REEN'       => new sfValidatorDateTime(array('required' => false)),
      'OBSERVACIONES'              => new sfValidatorString(array('max_length' => 200, 'required' => false)),
      'INSTANCIA'                  => new sfValidatorInteger(array('min' => -2147483648, 'max' => 2147483647, 'required' => false)),
      'BITACORA'                   => new sfValidatorInteger(array('min' => -2147483648, 'max' => 2147483647, 'required' => false)),
      'OBS_REMITIR'                => new sfValidatorString(array('max_length' => 300, 'required' => false)),
      'DESTINO_COMRECIBIDA'        => new sfValidatorString(array('max_length' => 50, 'required' => false)),
      'FECHA_MAXIMA_RESPUESTA_IMP' => new sfValidatorDateTime(array('required' => false)),
      'FECHA_DOCUMENTO'            => new sfValidatorDateTime(array('required' => false)),
      'VALOR_FACTURA'              => new sfValidatorNumber(array('required' => false)),
      'VALOR_FACTURA_US'           => new sfValidatorNumber(array('required' => false)),
      'MARCA_VINCULACION'          => new sfValidatorInteger(array('min' => -128, 'max' => 127, 'required' => false)),
      'FECHA_PAGO_FACTURA'         => new sfValidatorDateTime(array('required' => false)),
      'TIPOFIRMADIGITAL_ID'        => new sfValidatorInteger(array('min' => -2147483648, 'max' => 2147483647, 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('com_recibida[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'ComRecibida';
  }


}
