<?php

/**
 * ComRecibida filter form base class.
 *
 * @package    simad
 * @subpackage filter
 * @author     Your name here
 */
abstract class BaseComRecibidaFormFilter extends BaseFormFilterPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'REGIONAL_ID'             => new sfWidgetFormPropelChoice(array('model' => 'Regional', 'add_empty' => true)),
      'COMENVIADA_ID'           => new sfWidgetFormPropelChoice(array('model' => 'ComEnviada', 'add_empty' => true)),
      'ASUNTORECIBIDA_ID'       => new sfWidgetFormPropelChoice(array('model' => 'AsuntoRecibida', 'add_empty' => true)),
      'TIPOCOMRECIBIDA_ID'      => new sfWidgetFormPropelChoice(array('model' => 'TipoComRecibida', 'add_empty' => true)),
      'PERIODO_ID'              => new sfWidgetFormPropelChoice(array('model' => 'Periodo', 'add_empty' => true)),
      'DEPENDENCIA_ID'          => new sfWidgetFormPropelChoice(array('model' => 'Dependencia', 'add_empty' => true)),
      'ESTADODIGITALIZACION_ID' => new sfWidgetFormPropelChoice(array('model' => 'EstadoDigitalizacion', 'add_empty' => true)),
      'ESTADOCOMRECIBIDA_ID'    => new sfWidgetFormPropelChoice(array('model' => 'EstadoComRecibida', 'add_empty' => true)),
      'FORMARECEPCION_ID'       => new sfWidgetFormPropelChoice(array('model' => 'FormaRecepcion', 'add_empty' => true)),
      'CIUDAD_ID'               => new sfWidgetFormPropelChoice(array('model' => 'Ciudad', 'add_empty' => true)),
      'DIRECTORIOEXTERNO_ID'    => new sfWidgetFormPropelChoice(array('model' => 'DirectorioExterno', 'add_empty' => true)),
      'EMPRESA_MENSAJERIA_ID'   => new sfWidgetFormPropelChoice(array('model' => 'EmpresaMensajeria', 'add_empty' => true)),
      'TIPOIMPUESTO_ID'         => new sfWidgetFormPropelChoice(array('model' => 'TipoImpuesto', 'add_empty' => true)),
      'TIPORESPUESTA_ID'        => new sfWidgetFormPropelChoice(array('model' => 'TipoRespuesta', 'add_empty' => true)),
      'MEDIORESPUESTA_ID'       => new sfWidgetFormPropelChoice(array('model' => 'MedioRespuesta', 'add_empty' => true)),
      'RADICADO_ORIGEN'         => new sfWidgetFormFilterInput(),
      'FECHA_CREACION'          => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate())),
      'ASUNTO'                  => new sfWidgetFormFilterInput(),
      'NUMERO_RADICACION'       => new sfWidgetFormFilterInput(),
      'RADICADO'                => new sfWidgetFormFilterInput(),
      'ES_COPIA'                => new sfWidgetFormFilterInput(),
      'GUIA'                    => new sfWidgetFormFilterInput(),
      'FECHA_ENVIO_GUIA'        => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate())),
      'VALOR_GUIA'              => new sfWidgetFormFilterInput(),
      'FOLIOS'                  => new sfWidgetFormFilterInput(),
      'ANEXOS'                  => new sfWidgetFormFilterInput(),
      'RUTA'                    => new sfWidgetFormFilterInput(),
      'CODIGO_REEN_RESP'        => new sfWidgetFormFilterInput(),
      'OBS_REEN_RESP'           => new sfWidgetFormFilterInput(),
      'ESTAENTREGADO'           => new sfWidgetFormFilterInput(),
      'FECHA_MAXIMA_RESPUESTA'  => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate())),
      'FECHA_DE_ANULACION'      => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate())),
      'OBS_ANULACION'           => new sfWidgetFormFilterInput(),
      'MARCA'                   => new sfWidgetFormFilterInput(),
      'FECHA_RESPUESTA_REEN'    => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate())),
      'OBSERVACIONES'           => new sfWidgetFormFilterInput(),
      'INSTANCIA'               => new sfWidgetFormFilterInput(),
      'BITACORA'                => new sfWidgetFormFilterInput(),
      'OBS_REMITIR'             => new sfWidgetFormFilterInput(),
      'DESTINO_COMRECIBIDA'     => new sfWidgetFormFilterInput(),
      'FECHA_DOCUMENTO'         => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate())),
      'MARCA_VINCULACION'       => new sfWidgetFormFilterInput(),
      'TIPOFIRMADIGITAL_ID'     => new sfWidgetFormFilterInput(),
    ));

    $this->setValidators(array(
      'REGIONAL_ID'             => new sfValidatorPropelChoice(array('required' => false, 'model' => 'Regional', 'column' => 'REGIONAL_ID')),
      'COMENVIADA_ID'           => new sfValidatorPropelChoice(array('required' => false, 'model' => 'ComEnviada', 'column' => 'COMENVIADA_ID')),
      'ASUNTORECIBIDA_ID'       => new sfValidatorPropelChoice(array('required' => false, 'model' => 'AsuntoRecibida', 'column' => 'ASUNTORECIBIDA_ID')),
      'TIPOCOMRECIBIDA_ID'      => new sfValidatorPropelChoice(array('required' => false, 'model' => 'TipoComRecibida', 'column' => 'TIPOCOMRECIBIDA_ID')),
      'PERIODO_ID'              => new sfValidatorPropelChoice(array('required' => false, 'model' => 'Periodo', 'column' => 'PERIODO_ID')),
      'DEPENDENCIA_ID'          => new sfValidatorPropelChoice(array('required' => false, 'model' => 'Dependencia', 'column' => 'DEPENDENCIA_ID')),
      'ESTADODIGITALIZACION_ID' => new sfValidatorPropelChoice(array('required' => false, 'model' => 'EstadoDigitalizacion', 'column' => 'ESTADODIGITALIZACION_ID')),
      'ESTADOCOMRECIBIDA_ID'    => new sfValidatorPropelChoice(array('required' => false, 'model' => 'EstadoComRecibida', 'column' => 'ESTADOCOMRECIBIDA_ID')),
      'FORMARECEPCION_ID'       => new sfValidatorPropelChoice(array('required' => false, 'model' => 'FormaRecepcion', 'column' => 'FORMARECEPCION_ID')),
      'CIUDAD_ID'               => new sfValidatorPropelChoice(array('required' => false, 'model' => 'Ciudad', 'column' => 'CIUDAD_ID')),
      'DIRECTORIOEXTERNO_ID'    => new sfValidatorPropelChoice(array('required' => false, 'model' => 'DirectorioExterno', 'column' => 'DIRECTORIOEXTERNO_ID')),
      'EMPRESA_MENSAJERIA_ID'   => new sfValidatorPropelChoice(array('required' => false, 'model' => 'EmpresaMensajeria', 'column' => 'EMPRESA_MENSAJERIA_ID')),
      'TIPOIMPUESTO_ID'         => new sfValidatorPropelChoice(array('required' => false, 'model' => 'TipoImpuesto', 'column' => 'TIPOIMPUESTO_ID')),
      'TIPORESPUESTA_ID'        => new sfValidatorPropelChoice(array('required' => false, 'model' => 'TipoRespuesta', 'column' => 'TIPORESPUESTA_ID')),
      'MEDIORESPUESTA_ID'       => new sfValidatorPropelChoice(array('required' => false, 'model' => 'MedioRespuesta', 'column' => 'MEDIORESPUESTA_ID')),
      'RADICADO_ORIGEN'         => new sfValidatorPass(array('required' => false)),
      'FECHA_CREACION'          => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDate(array('required' => false)), 'to_date' => new sfValidatorDate(array('required' => false)))),
      'ASUNTO'                  => new sfValidatorPass(array('required' => false)),
      'NUMERO_RADICACION'       => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'RADICADO'                => new sfValidatorPass(array('required' => false)),
      'ES_COPIA'                => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'GUIA'                    => new sfValidatorPass(array('required' => false)),
      'FECHA_ENVIO_GUIA'        => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDate(array('required' => false)), 'to_date' => new sfValidatorDate(array('required' => false)))),
      'VALOR_GUIA'              => new sfValidatorSchemaFilter('text', new sfValidatorNumber(array('required' => false))),
      'FOLIOS'                  => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'ANEXOS'                  => new sfValidatorPass(array('required' => false)),
      'RUTA'                    => new sfValidatorPass(array('required' => false)),
      'CODIGO_REEN_RESP'        => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'OBS_REEN_RESP'           => new sfValidatorPass(array('required' => false)),
      'ESTAENTREGADO'           => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'FECHA_MAXIMA_RESPUESTA'  => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDate(array('required' => false)), 'to_date' => new sfValidatorDate(array('required' => false)))),
      'FECHA_DE_ANULACION'      => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDate(array('required' => false)), 'to_date' => new sfValidatorDate(array('required' => false)))),
      'OBS_ANULACION'           => new sfValidatorPass(array('required' => false)),
      'MARCA'                   => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'FECHA_RESPUESTA_REEN'    => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDate(array('required' => false)), 'to_date' => new sfValidatorDate(array('required' => false)))),
      'OBSERVACIONES'           => new sfValidatorPass(array('required' => false)),
      'INSTANCIA'               => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'BITACORA'                => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'OBS_REMITIR'             => new sfValidatorPass(array('required' => false)),
      'DESTINO_COMRECIBIDA'     => new sfValidatorPass(array('required' => false)),
      'FECHA_DOCUMENTO'         => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDate(array('required' => false)), 'to_date' => new sfValidatorDate(array('required' => false)))),
      'MARCA_VINCULACION'       => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'TIPOFIRMADIGITAL_ID'     => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
    ));

    $this->widgetSchema->setNameFormat('com_recibida_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'ComRecibida';
  }

  public function getFields()
  {
    return array(
      'COMRECIBIDA_ID'          => 'Number',
      'REGIONAL_ID'             => 'ForeignKey',
      'COMENVIADA_ID'           => 'ForeignKey',
      'ASUNTORECIBIDA_ID'       => 'ForeignKey',
      'TIPOCOMRECIBIDA_ID'      => 'ForeignKey',
      'PERIODO_ID'              => 'ForeignKey',
      'DEPENDENCIA_ID'          => 'ForeignKey',
      'ESTADODIGITALIZACION_ID' => 'ForeignKey',
      'ESTADOCOMRECIBIDA_ID'    => 'ForeignKey',
      'FORMARECEPCION_ID'       => 'ForeignKey',
      'CIUDAD_ID'               => 'ForeignKey',
      'DIRECTORIOEXTERNO_ID'    => 'ForeignKey',
      'EMPRESA_MENSAJERIA_ID'   => 'ForeignKey',
      'TIPOIMPUESTO_ID'         => 'ForeignKey',
      'TIPORESPUESTA_ID'        => 'ForeignKey',
      'MEDIORESPUESTA_ID'       => 'ForeignKey',
      'RADICADO_ORIGEN'         => 'Text',
      'FECHA_CREACION'          => 'Date',
      'ASUNTO'                  => 'Text',
      'NUMERO_RADICACION'       => 'Number',
      'RADICADO'                => 'Text',
      'ES_COPIA'                => 'Number',
      'GUIA'                    => 'Text',
      'FECHA_ENVIO_GUIA'        => 'Date',
      'VALOR_GUIA'              => 'Number',
      'FOLIOS'                  => 'Number',
      'ANEXOS'                  => 'Text',
      'RUTA'                    => 'Text',
      'CODIGO_REEN_RESP'        => 'Number',
      'OBS_REEN_RESP'           => 'Text',
      'ESTAENTREGADO'           => 'Number',
      'FECHA_MAXIMA_RESPUESTA'  => 'Date',
      'FECHA_DE_ANULACION'      => 'Date',
      'OBS_ANULACION'           => 'Text',
      'MARCA'                   => 'Number',
      'FECHA_RESPUESTA_REEN'    => 'Date',
      'OBSERVACIONES'           => 'Text',
      'INSTANCIA'               => 'Number',
      'BITACORA'                => 'Number',
      'OBS_REMITIR'             => 'Text',
      'DESTINO_COMRECIBIDA'     => 'Text',
      'FECHA_DOCUMENTO'         => 'Date',
      'MARCA_VINCULACION'       => 'Number',
      'TIPOFIRMADIGITAL_ID'     => 'Number',
    );
  }
}
