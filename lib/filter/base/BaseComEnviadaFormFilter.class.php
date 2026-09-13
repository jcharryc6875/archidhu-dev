<?php

/**
 * ComEnviada filter form base class.
 *
 * @package    simad
 * @subpackage filter
 * @author     Your name here
 */
abstract class BaseComEnviadaFormFilter extends BaseFormFilterPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'CIUDAD_ID'               => new sfWidgetFormPropelChoice(array('model' => 'Ciudad', 'add_empty' => true)),
      'DEPENDENCIA_ID'          => new sfWidgetFormPropelChoice(array('model' => 'Dependencia', 'add_empty' => true)),
      'ESTADOCOMENVIADA_ID'     => new sfWidgetFormPropelChoice(array('model' => 'EstadoComEnviada', 'add_empty' => true)),
      'EMPRESA_MENSAJERIA_ID'   => new sfWidgetFormPropelChoice(array('model' => 'EmpresaMensajeria', 'add_empty' => true)),
      'REGIONAL_ID'             => new sfWidgetFormPropelChoice(array('model' => 'Regional', 'add_empty' => true)),
      'PERIODO_ID'              => new sfWidgetFormPropelChoice(array('model' => 'Periodo', 'add_empty' => true)),
      'ESTADODIGITALIZACION_ID' => new sfWidgetFormPropelChoice(array('model' => 'EstadoDigitalizacion', 'add_empty' => true)),
      'NUMERO_RADICACION'       => new sfWidgetFormFilterInput(),
      'RADICADO'                => new sfWidgetFormFilterInput(),
      'ASUNTO'                  => new sfWidgetFormFilterInput(),
      'FECHA_CREACION'          => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate())),
      'GUIA'                    => new sfWidgetFormFilterInput(),
      'VALOR_GUIA'              => new sfWidgetFormFilterInput(),
      'FECHA_ENVIO_GUIA'        => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate())),
      'ES_COPIA'                => new sfWidgetFormFilterInput(),
      'FOLIOS'                  => new sfWidgetFormFilterInput(),
      'ANEXOS'                  => new sfWidgetFormFilterInput(),
      'ESTAENTREGADO'           => new sfWidgetFormFilterInput(),
      'CONTENIDO'               => new sfWidgetFormFilterInput(),
      'FECHA_DE_ANULACION'      => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate())),
      'OBS_ANULACION'           => new sfWidgetFormFilterInput(),
      'RUTA'                    => new sfWidgetFormFilterInput(),
      'MARCA'                   => new sfWidgetFormFilterInput(),
      'INSTANCIA'               => new sfWidgetFormFilterInput(),
      'BITACORA'                => new sfWidgetFormFilterInput(),
      'banco_id'                => new sfWidgetFormFilterInput(),
      'banco_radicado'          => new sfWidgetFormFilterInput(),
      'banco_numero_radicado'   => new sfWidgetFormFilterInput(),
      'FECHA_PRORROGRA'         => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate())),
      'FUNCIONARIO_DESTINO'     => new sfWidgetFormFilterInput(),
      'MARCA_VINCULACION'       => new sfWidgetFormFilterInput(),
      'PREFIJO'                 => new sfWidgetFormFilterInput(),
      'CARGO_DESTINATARIO'      => new sfWidgetFormFilterInput(),
      'DIRECCION_DESTINATARIO'  => new sfWidgetFormFilterInput(),
      'OBSERVACIONES_ENVIO'     => new sfWidgetFormFilterInput(),
      'IDIOMA_ID'               => new sfWidgetFormPropelChoice(array('model' => 'Idioma', 'add_empty' => true)),
      'PLANTILLASCOM_ID'        => new sfWidgetFormPropelChoice(array('model' => 'PlantillasCom', 'add_empty' => true)),
      'CONSECUTIVO'             => new sfWidgetFormFilterInput(),
      'URL_FILE_WORD'           => new sfWidgetFormFilterInput(),
      'IS_CREATE_WORD'          => new sfWidgetFormFilterInput(),
      'TIPOFIRMADIGITAL_ID'     => new sfWidgetFormFilterInput(),
      'USE_MEMBRETE'            => new sfWidgetFormFilterInput(),
      'CONSECUTIVO_RESP'        => new sfWidgetFormFilterInput(),
      'FIRMA_ELECTRONICA'       => new sfWidgetFormFilterInput(),
    ));

    $this->setValidators(array(
      'CIUDAD_ID'               => new sfValidatorPropelChoice(array('required' => false, 'model' => 'Ciudad', 'column' => 'CIUDAD_ID')),
      'DEPENDENCIA_ID'          => new sfValidatorPropelChoice(array('required' => false, 'model' => 'Dependencia', 'column' => 'DEPENDENCIA_ID')),
      'ESTADOCOMENVIADA_ID'     => new sfValidatorPropelChoice(array('required' => false, 'model' => 'EstadoComEnviada', 'column' => 'ESTADOCOMENVIADA_ID')),
      'EMPRESA_MENSAJERIA_ID'   => new sfValidatorPropelChoice(array('required' => false, 'model' => 'EmpresaMensajeria', 'column' => 'EMPRESA_MENSAJERIA_ID')),
      'REGIONAL_ID'             => new sfValidatorPropelChoice(array('required' => false, 'model' => 'Regional', 'column' => 'REGIONAL_ID')),
      'PERIODO_ID'              => new sfValidatorPropelChoice(array('required' => false, 'model' => 'Periodo', 'column' => 'PERIODO_ID')),
      'ESTADODIGITALIZACION_ID' => new sfValidatorPropelChoice(array('required' => false, 'model' => 'EstadoDigitalizacion', 'column' => 'ESTADODIGITALIZACION_ID')),
      'NUMERO_RADICACION'       => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'RADICADO'                => new sfValidatorPass(array('required' => false)),
      'ASUNTO'                  => new sfValidatorPass(array('required' => false)),
      'FECHA_CREACION'          => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDate(array('required' => false)), 'to_date' => new sfValidatorDate(array('required' => false)))),
      'GUIA'                    => new sfValidatorPass(array('required' => false)),
      'VALOR_GUIA'              => new sfValidatorSchemaFilter('text', new sfValidatorNumber(array('required' => false))),
      'FECHA_ENVIO_GUIA'        => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDate(array('required' => false)), 'to_date' => new sfValidatorDate(array('required' => false)))),
      'ES_COPIA'                => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'FOLIOS'                  => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'ANEXOS'                  => new sfValidatorPass(array('required' => false)),
      'ESTAENTREGADO'           => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'CONTENIDO'               => new sfValidatorPass(array('required' => false)),
      'FECHA_DE_ANULACION'      => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDate(array('required' => false)), 'to_date' => new sfValidatorDate(array('required' => false)))),
      'OBS_ANULACION'           => new sfValidatorPass(array('required' => false)),
      'RUTA'                    => new sfValidatorPass(array('required' => false)),
      'MARCA'                   => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'INSTANCIA'               => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'BITACORA'                => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'banco_id'                => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'banco_radicado'          => new sfValidatorPass(array('required' => false)),
      'banco_numero_radicado'   => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'FECHA_PRORROGRA'         => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDate(array('required' => false)), 'to_date' => new sfValidatorDate(array('required' => false)))),
      'FUNCIONARIO_DESTINO'     => new sfValidatorPass(array('required' => false)),
      'MARCA_VINCULACION'       => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'PREFIJO'                 => new sfValidatorPass(array('required' => false)),
      'CARGO_DESTINATARIO'      => new sfValidatorPass(array('required' => false)),
      'DIRECCION_DESTINATARIO'  => new sfValidatorPass(array('required' => false)),
      'OBSERVACIONES_ENVIO'     => new sfValidatorPass(array('required' => false)),
      'IDIOMA_ID'               => new sfValidatorPropelChoice(array('required' => false, 'model' => 'Idioma', 'column' => 'IDIOMA_ID')),
      'PLANTILLASCOM_ID'        => new sfValidatorPropelChoice(array('required' => false, 'model' => 'PlantillasCom', 'column' => 'PLANTILLASCOM_ID')),
      'CONSECUTIVO'             => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'URL_FILE_WORD'           => new sfValidatorPass(array('required' => false)),
      'IS_CREATE_WORD'          => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'TIPOFIRMADIGITAL_ID'     => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'USE_MEMBRETE'            => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'CONSECUTIVO_RESP'        => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'FIRMA_ELECTRONICA'       => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
    ));

    $this->widgetSchema->setNameFormat('com_enviada_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'ComEnviada';
  }

  public function getFields()
  {
    return array(
      'COMENVIADA_ID'           => 'Number',
      'CIUDAD_ID'               => 'ForeignKey',
      'DEPENDENCIA_ID'          => 'ForeignKey',
      'ESTADOCOMENVIADA_ID'     => 'ForeignKey',
      'EMPRESA_MENSAJERIA_ID'   => 'ForeignKey',
      'REGIONAL_ID'             => 'ForeignKey',
      'PERIODO_ID'              => 'ForeignKey',
      'ESTADODIGITALIZACION_ID' => 'ForeignKey',
      'NUMERO_RADICACION'       => 'Number',
      'RADICADO'                => 'Text',
      'ASUNTO'                  => 'Text',
      'FECHA_CREACION'          => 'Date',
      'GUIA'                    => 'Text',
      'VALOR_GUIA'              => 'Number',
      'FECHA_ENVIO_GUIA'        => 'Date',
      'ES_COPIA'                => 'Number',
      'FOLIOS'                  => 'Number',
      'ANEXOS'                  => 'Text',
      'ESTAENTREGADO'           => 'Number',
      'CONTENIDO'               => 'Text',
      'FECHA_DE_ANULACION'      => 'Date',
      'OBS_ANULACION'           => 'Text',
      'RUTA'                    => 'Text',
      'MARCA'                   => 'Number',
      'INSTANCIA'               => 'Number',
      'BITACORA'                => 'Number',
      'banco_id'                => 'Number',
      'banco_radicado'          => 'Text',
      'banco_numero_radicado'   => 'Number',
      'FECHA_PRORROGRA'         => 'Date',
      'FUNCIONARIO_DESTINO'     => 'Text',
      'MARCA_VINCULACION'       => 'Number',
      'PREFIJO'                 => 'Text',
      'CARGO_DESTINATARIO'      => 'Text',
      'DIRECCION_DESTINATARIO'  => 'Text',
      'OBSERVACIONES_ENVIO'     => 'Text',
      'IDIOMA_ID'               => 'ForeignKey',
      'PLANTILLASCOM_ID'        => 'ForeignKey',
      'CONSECUTIVO'             => 'Number',
      'URL_FILE_WORD'           => 'Text',
      'IS_CREATE_WORD'          => 'Number',
      'TIPOFIRMADIGITAL_ID'     => 'Number',
      'USE_MEMBRETE'            => 'Number',
      'CONSECUTIVO_RESP'        => 'Number',
      'FIRMA_ELECTRONICA'       => 'Number',
    );
  }
}
