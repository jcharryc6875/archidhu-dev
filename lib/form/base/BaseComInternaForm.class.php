<?php

/**
 * ComInterna form base class.
 *
 * @method ComInterna getObject() Returns the current form's model object
 *
 * @package    ##PROJECT_NAME##
 * @subpackage form
 * @author     ##AUTHOR_NAME##
 */
abstract class BaseComInternaForm extends BaseFormPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'COMINTERNA_ID'           => new sfWidgetFormInputHidden(),
      'EMPRESA_MENSAJERIA_ID'   => new sfWidgetFormPropelChoice(array('model' => 'EmpresaMensajeria', 'add_empty' => true)),
      'ESTADOCOMINTERNA_ID'     => new sfWidgetFormPropelChoice(array('model' => 'EstadoComInterna', 'add_empty' => false)),
      'PERIODO_ID'              => new sfWidgetFormPropelChoice(array('model' => 'Periodo', 'add_empty' => false)),
      'DEPENDENCIA_ID'          => new sfWidgetFormPropelChoice(array('model' => 'Dependencia', 'add_empty' => false)),
      'TIPOCOMINTERNA_ID'       => new sfWidgetFormPropelChoice(array('model' => 'TipoComInterna', 'add_empty' => false)),
      'REGIONAL_ID'             => new sfWidgetFormPropelChoice(array('model' => 'Regional', 'add_empty' => false)),
      'ESTADODIGITALIZACION_ID' => new sfWidgetFormPropelChoice(array('model' => 'EstadoDigitalizacion', 'add_empty' => false)),
      'FECHA_CREACION'          => new sfWidgetFormDateTime(),
      'REFERENCIA'              => new sfWidgetFormInputText(),
      'CONTENIDO'               => new sfWidgetFormTextarea(),
      'RUTA'                    => new sfWidgetFormInputText(),
      'NUMERO_RADICACION'       => new sfWidgetFormInputText(),
      'RADICADO'                => new sfWidgetFormInputText(),
      'ES_COPIA'                => new sfWidgetFormInputText(),
      'GUIA'                    => new sfWidgetFormInputText(),
      'FECHA_ENVIO_GUIA'        => new sfWidgetFormDateTime(),
      'VALOR_GUIA'              => new sfWidgetFormInputText(),
      'CODIGO_REEN_RESP'        => new sfWidgetFormInputText(),
      'OBS_REEN_RESP'           => new sfWidgetFormInputText(),
      'FOLIOS'                  => new sfWidgetFormInputText(),
      'ANEXOS'                  => new sfWidgetFormInputText(),
      'ESTAENTREGADO'           => new sfWidgetFormInputText(),
      'FECHA_DE_ANULACION'      => new sfWidgetFormDateTime(),
      'OBS_ANULACION'           => new sfWidgetFormInputText(),
      'MARCA'                   => new sfWidgetFormInputText(),
      'INSTANCIA'               => new sfWidgetFormInputText(),
      'BITACORA'                => new sfWidgetFormInputText(),
      'FECHA_MAXIMA_RESPUESTA'  => new sfWidgetFormDateTime(),
      'MARCA_VINCULACION'       => new sfWidgetFormInputText(),
      'REQUIERE_RESPUESTA'      => new sfWidgetFormInputText(),
      'IS_CREATE_WORD'          => new sfWidgetFormInputText(),
      'URL_FILE_WORD'           => new sfWidgetFormInputText(),
      'TIPOFIRMADIGITAL_ID'     => new sfWidgetFormInputText(),
      'USE_MEMBRETE'            => new sfWidgetFormInputText(),
      'CONSECUTIVO_RESP'        => new sfWidgetFormInputText(),
      'FIRMA_ELECTRONICA'       => new sfWidgetFormInputText(),
    ));

    $this->setValidators(array(
      'COMINTERNA_ID'           => new sfValidatorChoice(array('choices' => array($this->getObject()->getCominternaId()), 'empty_value' => $this->getObject()->getCominternaId(), 'required' => false)),
      'EMPRESA_MENSAJERIA_ID'   => new sfValidatorPropelChoice(array('model' => 'EmpresaMensajeria', 'column' => 'EMPRESA_MENSAJERIA_ID', 'required' => false)),
      'ESTADOCOMINTERNA_ID'     => new sfValidatorPropelChoice(array('model' => 'EstadoComInterna', 'column' => 'ESTADOCOMINTERNA_ID')),
      'PERIODO_ID'              => new sfValidatorPropelChoice(array('model' => 'Periodo', 'column' => 'PERIODO_ID')),
      'DEPENDENCIA_ID'          => new sfValidatorPropelChoice(array('model' => 'Dependencia', 'column' => 'DEPENDENCIA_ID')),
      'TIPOCOMINTERNA_ID'       => new sfValidatorPropelChoice(array('model' => 'TipoComInterna', 'column' => 'TIPOCOMINTERNA_ID')),
      'REGIONAL_ID'             => new sfValidatorPropelChoice(array('model' => 'Regional', 'column' => 'REGIONAL_ID')),
      'ESTADODIGITALIZACION_ID' => new sfValidatorPropelChoice(array('model' => 'EstadoDigitalizacion', 'column' => 'ESTADODIGITALIZACION_ID')),
      'FECHA_CREACION'          => new sfValidatorDateTime(array('required' => false)),
      'REFERENCIA'              => new sfValidatorString(array('max_length' => 500, 'required' => false)),
      'CONTENIDO'               => new sfValidatorString(array('required' => false)),
      'RUTA'                    => new sfValidatorString(array('max_length' => 900, 'required' => false)),
      'NUMERO_RADICACION'       => new sfValidatorInteger(array('min' => -2147483648, 'max' => 2147483647, 'required' => false)),
      'RADICADO'                => new sfValidatorString(array('max_length' => 50, 'required' => false)),
      'ES_COPIA'                => new sfValidatorInteger(array('min' => -128, 'max' => 127, 'required' => false)),
      'GUIA'                    => new sfValidatorString(array('max_length' => 50, 'required' => false)),
      'FECHA_ENVIO_GUIA'        => new sfValidatorDateTime(array('required' => false)),
      'VALOR_GUIA'              => new sfValidatorNumber(array('required' => false)),
      'CODIGO_REEN_RESP'        => new sfValidatorInteger(array('min' => -2147483648, 'max' => 2147483647, 'required' => false)),
      'OBS_REEN_RESP'           => new sfValidatorString(array('max_length' => 200, 'required' => false)),
      'FOLIOS'                  => new sfValidatorInteger(array('min' => -2147483648, 'max' => 2147483647, 'required' => false)),
      'ANEXOS'                  => new sfValidatorString(array('max_length' => 200, 'required' => false)),
      'ESTAENTREGADO'           => new sfValidatorInteger(array('min' => -128, 'max' => 127, 'required' => false)),
      'FECHA_DE_ANULACION'      => new sfValidatorDateTime(array('required' => false)),
      'OBS_ANULACION'           => new sfValidatorString(array('max_length' => 200, 'required' => false)),
      'MARCA'                   => new sfValidatorInteger(array('min' => -2147483648, 'max' => 2147483647, 'required' => false)),
      'INSTANCIA'               => new sfValidatorInteger(array('min' => -2147483648, 'max' => 2147483647, 'required' => false)),
      'BITACORA'                => new sfValidatorInteger(array('min' => -2147483648, 'max' => 2147483647, 'required' => false)),
      'FECHA_MAXIMA_RESPUESTA'  => new sfValidatorDateTime(array('required' => false)),
      'MARCA_VINCULACION'       => new sfValidatorInteger(array('min' => -128, 'max' => 127, 'required' => false)),
      'REQUIERE_RESPUESTA'      => new sfValidatorInteger(array('min' => -128, 'max' => 127, 'required' => false)),
      'IS_CREATE_WORD'          => new sfValidatorInteger(array('min' => -128, 'max' => 127, 'required' => false)),
      'URL_FILE_WORD'           => new sfValidatorString(array('max_length' => 200, 'required' => false)),
      'TIPOFIRMADIGITAL_ID'     => new sfValidatorInteger(array('min' => -2147483648, 'max' => 2147483647, 'required' => false)),
      'USE_MEMBRETE'            => new sfValidatorInteger(array('min' => -128, 'max' => 127, 'required' => false)),
      'CONSECUTIVO_RESP'        => new sfValidatorInteger(array('min' => -2147483648, 'max' => 2147483647, 'required' => false)),
      'FIRMA_ELECTRONICA'       => new sfValidatorInteger(array('min' => -128, 'max' => 127, 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('com_interna[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'ComInterna';
  }


}
