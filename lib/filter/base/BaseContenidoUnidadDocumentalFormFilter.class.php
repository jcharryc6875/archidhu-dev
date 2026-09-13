<?php

/**
 * ContenidoUnidadDocumental filter form base class.
 *
 * @package    simad
 * @subpackage filter
 * @author     Your name here
 */
abstract class BaseContenidoUnidadDocumentalFormFilter extends BaseFormFilterPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'UNIDADDOCUMENTAL_ID'          => new sfWidgetFormPropelChoice(array('model' => 'UnidadDocumental', 'add_empty' => true)),
      'VERIFICACIONCONTUNIDADDOC_ID' => new sfWidgetFormPropelChoice(array('model' => 'VerificacionContUnidadDoc', 'add_empty' => true)),
      'TIPODOCUMENTAL_ID'            => new sfWidgetFormPropelChoice(array('model' => 'TipoDocumental', 'add_empty' => true)),
      'ESTADOCONTENIDOUNIDADDOC_ID'  => new sfWidgetFormPropelChoice(array('model' => 'EstadoContenidoUnidadDocumental', 'add_empty' => true)),
      'USUARIO_ID'                   => new sfWidgetFormPropelChoice(array('model' => 'Usuario', 'add_empty' => true)),
      'DESCRIPCION'                  => new sfWidgetFormFilterInput(),
      'RUTA'                         => new sfWidgetFormFilterInput(),
      'FECHA_CREACION'               => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate())),
      'FOLIOS'                       => new sfWidgetFormFilterInput(),
      'CREADO_POR_WEB'               => new sfWidgetFormFilterInput(),
      'FECHA_DOCUMENTO'              => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate())),
      'MARCA'                        => new sfWidgetFormFilterInput(),
      'SOPORTEUNIDADDOCUMENTAL_ID'   => new sfWidgetFormFilterInput(),
      'TIPOFIRMADIGITAL_ID'          => new sfWidgetFormFilterInput(),
      'VINCULO_REGISTRO'             => new sfWidgetFormFilterInput(),
    ));

    $this->setValidators(array(
      'UNIDADDOCUMENTAL_ID'          => new sfValidatorPropelChoice(array('required' => false, 'model' => 'UnidadDocumental', 'column' => 'UNIDADDOCUMENTAL_ID')),
      'VERIFICACIONCONTUNIDADDOC_ID' => new sfValidatorPropelChoice(array('required' => false, 'model' => 'VerificacionContUnidadDoc', 'column' => 'VERIFICACIONCONTUNIDADDOC_ID')),
      'TIPODOCUMENTAL_ID'            => new sfValidatorPropelChoice(array('required' => false, 'model' => 'TipoDocumental', 'column' => 'TIPODOCUMENTAL_ID')),
      'ESTADOCONTENIDOUNIDADDOC_ID'  => new sfValidatorPropelChoice(array('required' => false, 'model' => 'EstadoContenidoUnidadDocumental', 'column' => 'ESTADOCONTENIDOUNIDADDOC_ID')),
      'USUARIO_ID'                   => new sfValidatorPropelChoice(array('required' => false, 'model' => 'Usuario', 'column' => 'USUARIO_ID')),
      'DESCRIPCION'                  => new sfValidatorPass(array('required' => false)),
      'RUTA'                         => new sfValidatorPass(array('required' => false)),
      'FECHA_CREACION'               => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDate(array('required' => false)), 'to_date' => new sfValidatorDate(array('required' => false)))),
      'FOLIOS'                       => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'CREADO_POR_WEB'               => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'FECHA_DOCUMENTO'              => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDate(array('required' => false)), 'to_date' => new sfValidatorDate(array('required' => false)))),
      'MARCA'                        => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'SOPORTEUNIDADDOCUMENTAL_ID'   => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'TIPOFIRMADIGITAL_ID'          => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'VINCULO_REGISTRO'             => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
    ));

    $this->widgetSchema->setNameFormat('contenido_unidad_documental_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'ContenidoUnidadDocumental';
  }

  public function getFields()
  {
    return array(
      'CONTENIDOUNIDADDOCUMENTAL_ID' => 'Number',
      'UNIDADDOCUMENTAL_ID'          => 'ForeignKey',
      'VERIFICACIONCONTUNIDADDOC_ID' => 'ForeignKey',
      'TIPODOCUMENTAL_ID'            => 'ForeignKey',
      'ESTADOCONTENIDOUNIDADDOC_ID'  => 'ForeignKey',
      'USUARIO_ID'                   => 'ForeignKey',
      'DESCRIPCION'                  => 'Text',
      'RUTA'                         => 'Text',
      'FECHA_CREACION'               => 'Date',
      'FOLIOS'                       => 'Number',
      'CREADO_POR_WEB'               => 'Number',
      'FECHA_DOCUMENTO'              => 'Date',
      'MARCA'                        => 'Number',
      'SOPORTEUNIDADDOCUMENTAL_ID'   => 'Number',
      'TIPOFIRMADIGITAL_ID'          => 'Number',
      'VINCULO_REGISTRO'             => 'Number',
    );
  }
}
