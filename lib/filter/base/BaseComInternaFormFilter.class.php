<?php

/**
 * ComInterna filter form base class.
 *
 * @package    simad
 * @subpackage filter
 * @author     Your name here
 */
abstract class BaseComInternaFormFilter extends BaseFormFilterPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'EMPRESA_MENSAJERIA_ID'   => new sfWidgetFormPropelChoice(array('model' => 'EmpresaMensajeria', 'add_empty' => true)),
      'ESTADOCOMINTERNA_ID'     => new sfWidgetFormPropelChoice(array('model' => 'EstadoComInterna', 'add_empty' => true)),
      'PERIODO_ID'              => new sfWidgetFormPropelChoice(array('model' => 'Periodo', 'add_empty' => true)),
      'DEPENDENCIA_ID'          => new sfWidgetFormPropelChoice(array('model' => 'Dependencia', 'add_empty' => true)),
      'TIPOCOMINTERNA_ID'       => new sfWidgetFormPropelChoice(array('model' => 'TipoComInterna', 'add_empty' => true)),
      'REGIONAL_ID'             => new sfWidgetFormPropelChoice(array('model' => 'Regional', 'add_empty' => true)),
      'ESTADODIGITALIZACION_ID' => new sfWidgetFormPropelChoice(array('model' => 'EstadoDigitalizacion', 'add_empty' => true)),
      'FECHA_CREACION'          => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate())),
      'REFERENCIA'              => new sfWidgetFormFilterInput(),
      'CONTENIDO'               => new sfWidgetFormFilterInput(),
      'RUTA'                    => new sfWidgetFormFilterInput(),
      'NUMERO_RADICACION'       => new sfWidgetFormFilterInput(),
      'RADICADO'                => new sfWidgetFormFilterInput(),
      'ES_COPIA'                => new sfWidgetFormFilterInput(),
      'GUIA'                    => new sfWidgetFormFilterInput(),
      'FECHA_ENVIO_GUIA'        => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate())),
      'VALOR_GUIA'              => new sfWidgetFormFilterInput(),
      'CODIGO_REEN_RESP'        => new sfWidgetFormFilterInput(),
      'OBS_REEN_RESP'           => new sfWidgetFormFilterInput(),
      'FOLIOS'                  => new sfWidgetFormFilterInput(),
      'ANEXOS'                  => new sfWidgetFormFilterInput(),
      'ESTAENTREGADO'           => new sfWidgetFormFilterInput(),
      'FECHA_DE_ANULACION'      => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate())),
      'OBS_ANULACION'           => new sfWidgetFormFilterInput(),
      'MARCA'                   => new sfWidgetFormFilterInput(),
      'INSTANCIA'               => new sfWidgetFormFilterInput(),
      'BITACORA'                => new sfWidgetFormFilterInput(),
      'FECHA_MAXIMA_RESPUESTA'  => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate())),
      'MARCA_VINCULACION'       => new sfWidgetFormFilterInput(),
      'REQUIERE_RESPUESTA'      => new sfWidgetFormFilterInput(),
      'IS_CREATE_WORD'          => new sfWidgetFormFilterInput(),
      'URL_FILE_WORD'           => new sfWidgetFormFilterInput(),
      'TIPOFIRMADIGITAL_ID'     => new sfWidgetFormFilterInput(),
      'USE_MEMBRETE'            => new sfWidgetFormFilterInput(),
      'CONSECUTIVO_RESP'        => new sfWidgetFormFilterInput(),
      'FIRMA_ELECTRONICA'       => new sfWidgetFormFilterInput(),
    ));

    $this->setValidators(array(
      'EMPRESA_MENSAJERIA_ID'   => new sfValidatorPropelChoice(array('required' => false, 'model' => 'EmpresaMensajeria', 'column' => 'EMPRESA_MENSAJERIA_ID')),
      'ESTADOCOMINTERNA_ID'     => new sfValidatorPropelChoice(array('required' => false, 'model' => 'EstadoComInterna', 'column' => 'ESTADOCOMINTERNA_ID')),
      'PERIODO_ID'              => new sfValidatorPropelChoice(array('required' => false, 'model' => 'Periodo', 'column' => 'PERIODO_ID')),
      'DEPENDENCIA_ID'          => new sfValidatorPropelChoice(array('required' => false, 'model' => 'Dependencia', 'column' => 'DEPENDENCIA_ID')),
      'TIPOCOMINTERNA_ID'       => new sfValidatorPropelChoice(array('required' => false, 'model' => 'TipoComInterna', 'column' => 'TIPOCOMINTERNA_ID')),
      'REGIONAL_ID'             => new sfValidatorPropelChoice(array('required' => false, 'model' => 'Regional', 'column' => 'REGIONAL_ID')),
      'ESTADODIGITALIZACION_ID' => new sfValidatorPropelChoice(array('required' => false, 'model' => 'EstadoDigitalizacion', 'column' => 'ESTADODIGITALIZACION_ID')),
      'FECHA_CREACION'          => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDate(array('required' => false)), 'to_date' => new sfValidatorDate(array('required' => false)))),
      'REFERENCIA'              => new sfValidatorPass(array('required' => false)),
      'CONTENIDO'               => new sfValidatorPass(array('required' => false)),
      'RUTA'                    => new sfValidatorPass(array('required' => false)),
      'NUMERO_RADICACION'       => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'RADICADO'                => new sfValidatorPass(array('required' => false)),
      'ES_COPIA'                => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'GUIA'                    => new sfValidatorPass(array('required' => false)),
      'FECHA_ENVIO_GUIA'        => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDate(array('required' => false)), 'to_date' => new sfValidatorDate(array('required' => false)))),
      'VALOR_GUIA'              => new sfValidatorSchemaFilter('text', new sfValidatorNumber(array('required' => false))),
      'CODIGO_REEN_RESP'        => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'OBS_REEN_RESP'           => new sfValidatorPass(array('required' => false)),
      'FOLIOS'                  => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'ANEXOS'                  => new sfValidatorPass(array('required' => false)),
      'ESTAENTREGADO'           => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'FECHA_DE_ANULACION'      => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDate(array('required' => false)), 'to_date' => new sfValidatorDate(array('required' => false)))),
      'OBS_ANULACION'           => new sfValidatorPass(array('required' => false)),
      'MARCA'                   => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'INSTANCIA'               => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'BITACORA'                => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'FECHA_MAXIMA_RESPUESTA'  => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDate(array('required' => false)), 'to_date' => new sfValidatorDate(array('required' => false)))),
      'MARCA_VINCULACION'       => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'REQUIERE_RESPUESTA'      => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'IS_CREATE_WORD'          => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'URL_FILE_WORD'           => new sfValidatorPass(array('required' => false)),
      'TIPOFIRMADIGITAL_ID'     => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'USE_MEMBRETE'            => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'CONSECUTIVO_RESP'        => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'FIRMA_ELECTRONICA'       => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
    ));

    $this->widgetSchema->setNameFormat('com_interna_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'ComInterna';
  }

  public function getFields()
  {
    return array(
      'COMINTERNA_ID'           => 'Number',
      'EMPRESA_MENSAJERIA_ID'   => 'ForeignKey',
      'ESTADOCOMINTERNA_ID'     => 'ForeignKey',
      'PERIODO_ID'              => 'ForeignKey',
      'DEPENDENCIA_ID'          => 'ForeignKey',
      'TIPOCOMINTERNA_ID'       => 'ForeignKey',
      'REGIONAL_ID'             => 'ForeignKey',
      'ESTADODIGITALIZACION_ID' => 'ForeignKey',
      'FECHA_CREACION'          => 'Date',
      'REFERENCIA'              => 'Text',
      'CONTENIDO'               => 'Text',
      'RUTA'                    => 'Text',
      'NUMERO_RADICACION'       => 'Number',
      'RADICADO'                => 'Text',
      'ES_COPIA'                => 'Number',
      'GUIA'                    => 'Text',
      'FECHA_ENVIO_GUIA'        => 'Date',
      'VALOR_GUIA'              => 'Number',
      'CODIGO_REEN_RESP'        => 'Number',
      'OBS_REEN_RESP'           => 'Text',
      'FOLIOS'                  => 'Number',
      'ANEXOS'                  => 'Text',
      'ESTAENTREGADO'           => 'Number',
      'FECHA_DE_ANULACION'      => 'Date',
      'OBS_ANULACION'           => 'Text',
      'MARCA'                   => 'Number',
      'INSTANCIA'               => 'Number',
      'BITACORA'                => 'Number',
      'FECHA_MAXIMA_RESPUESTA'  => 'Date',
      'MARCA_VINCULACION'       => 'Number',
      'REQUIERE_RESPUESTA'      => 'Number',
      'IS_CREATE_WORD'          => 'Number',
      'URL_FILE_WORD'           => 'Text',
      'TIPOFIRMADIGITAL_ID'     => 'Number',
      'USE_MEMBRETE'            => 'Number',
      'CONSECUTIVO_RESP'        => 'Number',
      'FIRMA_ELECTRONICA'       => 'Number',
    );
  }
}
