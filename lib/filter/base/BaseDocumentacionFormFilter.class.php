<?php

/**
 * Documentacion filter form base class.
 *
 * @package    simad
 * @subpackage filter
 * @author     Your name here
 */
abstract class BaseDocumentacionFormFilter extends BaseFormFilterPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'ESTADODOCUMENTACION_ID'          => new sfWidgetFormPropelChoice(array('model' => 'EstadoDocumentacion', 'add_empty' => true)),
      'IDIOMA_ID'                       => new sfWidgetFormPropelChoice(array('model' => 'Idioma', 'add_empty' => true)),
      'TIPODOCUMENTACION_ID'            => new sfWidgetFormPropelChoice(array('model' => 'TipoDocumentacion', 'add_empty' => true)),
      'TITULO'                          => new sfWidgetFormFilterInput(),
      'OTRA_INFORMACION_TITULO'         => new sfWidgetFormFilterInput(),
      'PRIMERA_MENCION_RESPONSABILIDAD' => new sfWidgetFormFilterInput(),
      'MENCION_RESPONSABILIDAD'         => new sfWidgetFormFilterInput(),
      'NUMERO_CLASIFICACION'            => new sfWidgetFormFilterInput(),
      'DESCRIPTORES_TEMATICOS'          => new sfWidgetFormFilterInput(),
      'CODIGO_BARRAS'                   => new sfWidgetFormFilterInput(),
      'UBICACION'                       => new sfWidgetFormFilterInput(),
      'RUTA'                            => new sfWidgetFormFilterInput(),
      'FECHA_CREACION'                  => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate())),
      'FECHA_MODIFICACION'              => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate())),
    ));

    $this->setValidators(array(
      'ESTADODOCUMENTACION_ID'          => new sfValidatorPropelChoice(array('required' => false, 'model' => 'EstadoDocumentacion', 'column' => 'ESTADODOCUMENTACION_ID')),
      'IDIOMA_ID'                       => new sfValidatorPropelChoice(array('required' => false, 'model' => 'Idioma', 'column' => 'IDIOMA_ID')),
      'TIPODOCUMENTACION_ID'            => new sfValidatorPropelChoice(array('required' => false, 'model' => 'TipoDocumentacion', 'column' => 'TIPODOCUMENTACION_ID')),
      'TITULO'                          => new sfValidatorPass(array('required' => false)),
      'OTRA_INFORMACION_TITULO'         => new sfValidatorPass(array('required' => false)),
      'PRIMERA_MENCION_RESPONSABILIDAD' => new sfValidatorPass(array('required' => false)),
      'MENCION_RESPONSABILIDAD'         => new sfValidatorPass(array('required' => false)),
      'NUMERO_CLASIFICACION'            => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'DESCRIPTORES_TEMATICOS'          => new sfValidatorPass(array('required' => false)),
      'CODIGO_BARRAS'                   => new sfValidatorPass(array('required' => false)),
      'UBICACION'                       => new sfValidatorPass(array('required' => false)),
      'RUTA'                            => new sfValidatorPass(array('required' => false)),
      'FECHA_CREACION'                  => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDate(array('required' => false)), 'to_date' => new sfValidatorDate(array('required' => false)))),
      'FECHA_MODIFICACION'              => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDate(array('required' => false)), 'to_date' => new sfValidatorDate(array('required' => false)))),
    ));

    $this->widgetSchema->setNameFormat('documentacion_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'Documentacion';
  }

  public function getFields()
  {
    return array(
      'DOCUMENTACION_ID'                => 'Number',
      'ESTADODOCUMENTACION_ID'          => 'ForeignKey',
      'IDIOMA_ID'                       => 'ForeignKey',
      'TIPODOCUMENTACION_ID'            => 'ForeignKey',
      'TITULO'                          => 'Text',
      'OTRA_INFORMACION_TITULO'         => 'Text',
      'PRIMERA_MENCION_RESPONSABILIDAD' => 'Text',
      'MENCION_RESPONSABILIDAD'         => 'Text',
      'NUMERO_CLASIFICACION'            => 'Number',
      'DESCRIPTORES_TEMATICOS'          => 'Text',
      'CODIGO_BARRAS'                   => 'Text',
      'UBICACION'                       => 'Text',
      'RUTA'                            => 'Text',
      'FECHA_CREACION'                  => 'Date',
      'FECHA_MODIFICACION'              => 'Date',
    );
  }
}
