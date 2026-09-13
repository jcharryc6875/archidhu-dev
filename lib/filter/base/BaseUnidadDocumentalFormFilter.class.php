<?php

/**
 * UnidadDocumental filter form base class.
 *
 * @package    simad
 * @subpackage filter
 * @author     Your name here
 */
abstract class BaseUnidadDocumentalFormFilter extends BaseFormFilterPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'LOCALIZACIONUNIDADDOCUMENTAL_ID' => new sfWidgetFormPropelChoice(array('model' => 'LocalizacionUnidadDocumental', 'add_empty' => true)),
      'REGIONAL_ID'                     => new sfWidgetFormPropelChoice(array('model' => 'Regional', 'add_empty' => true)),
      'SOPORTEUNIDADDOCUMENTAL_ID'      => new sfWidgetFormPropelChoice(array('model' => 'SoporteUnidadDocumental', 'add_empty' => true)),
      'ESTADOUNIDADDOCUMENTAL_ID'       => new sfWidgetFormPropelChoice(array('model' => 'EstadoUnidadDocumental', 'add_empty' => true)),
      'FRECUENCIACONSULTA_ID'           => new sfWidgetFormPropelChoice(array('model' => 'FrecuenciaConsulta', 'add_empty' => true)),
      'UNIDADCONSERVADORA_ID'           => new sfWidgetFormPropelChoice(array('model' => 'UnidadConservadora', 'add_empty' => true)),
      'SUBSERIE_ID'                     => new sfWidgetFormPropelChoice(array('model' => 'Subserie', 'add_empty' => true)),
      'CODIGO_BARRAS'                   => new sfWidgetFormFilterInput(),
      'FECHA_APERTURA'                  => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate())),
      'FECHA_CIERRE'                    => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate())),
      'TITULO'                          => new sfWidgetFormFilterInput(),
      'CONTENIDO'                       => new sfWidgetFormFilterInput(),
      'CREADO_POR_WEB'                  => new sfWidgetFormFilterInput(),
      'UBICACIONENGESTION'              => new sfWidgetFormFilterInput(),
      'UBICACIONENCENTRAL'              => new sfWidgetFormFilterInput(),
      'UBICACIONENHISTORICO'            => new sfWidgetFormFilterInput(),
      'FOLIOS'                          => new sfWidgetFormFilterInput(),
      'FECHA_CREACION'                  => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate())),
      'NOTAS'                           => new sfWidgetFormFilterInput(),
      'FECHAVENCIMIENTO'                => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate())),
      'VOLUMEN'                         => new sfWidgetFormFilterInput(),
      'ESTADOTRANSFERENCIA'             => new sfWidgetFormFilterInput(),
      'MARCA'                           => new sfWidgetFormFilterInput(),
      'MARCA_ELIMINACION'               => new sfWidgetFormFilterInput(),
      'OBS_ELIMINACION'                 => new sfWidgetFormFilterInput(),
      'NUMERO_CAJA'                     => new sfWidgetFormFilterInput(),
      'NUMERO_IDENTIFICACION'           => new sfWidgetFormFilterInput(),
    ));

    $this->setValidators(array(
      'LOCALIZACIONUNIDADDOCUMENTAL_ID' => new sfValidatorPropelChoice(array('required' => false, 'model' => 'LocalizacionUnidadDocumental', 'column' => 'LOCALIZACIONUNIDADDOCUMENTAL_ID')),
      'REGIONAL_ID'                     => new sfValidatorPropelChoice(array('required' => false, 'model' => 'Regional', 'column' => 'REGIONAL_ID')),
      'SOPORTEUNIDADDOCUMENTAL_ID'      => new sfValidatorPropelChoice(array('required' => false, 'model' => 'SoporteUnidadDocumental', 'column' => 'SOPORTEUNIDADDOCUMENTAL_ID')),
      'ESTADOUNIDADDOCUMENTAL_ID'       => new sfValidatorPropelChoice(array('required' => false, 'model' => 'EstadoUnidadDocumental', 'column' => 'ESTADOUNIDADDOCUMENTAL_ID')),
      'FRECUENCIACONSULTA_ID'           => new sfValidatorPropelChoice(array('required' => false, 'model' => 'FrecuenciaConsulta', 'column' => 'FRECUENCIACONSULTA_ID')),
      'UNIDADCONSERVADORA_ID'           => new sfValidatorPropelChoice(array('required' => false, 'model' => 'UnidadConservadora', 'column' => 'UNIDADCONSERVADORA_ID')),
      'SUBSERIE_ID'                     => new sfValidatorPropelChoice(array('required' => false, 'model' => 'Subserie', 'column' => 'SUBSERIE_ID')),
      'CODIGO_BARRAS'                   => new sfValidatorPass(array('required' => false)),
      'FECHA_APERTURA'                  => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDate(array('required' => false)), 'to_date' => new sfValidatorDate(array('required' => false)))),
      'FECHA_CIERRE'                    => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDate(array('required' => false)), 'to_date' => new sfValidatorDate(array('required' => false)))),
      'TITULO'                          => new sfValidatorPass(array('required' => false)),
      'CONTENIDO'                       => new sfValidatorPass(array('required' => false)),
      'CREADO_POR_WEB'                  => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'UBICACIONENGESTION'              => new sfValidatorPass(array('required' => false)),
      'UBICACIONENCENTRAL'              => new sfValidatorPass(array('required' => false)),
      'UBICACIONENHISTORICO'            => new sfValidatorPass(array('required' => false)),
      'FOLIOS'                          => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'FECHA_CREACION'                  => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDate(array('required' => false)), 'to_date' => new sfValidatorDate(array('required' => false)))),
      'NOTAS'                           => new sfValidatorPass(array('required' => false)),
      'FECHAVENCIMIENTO'                => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDate(array('required' => false)), 'to_date' => new sfValidatorDate(array('required' => false)))),
      'VOLUMEN'                         => new sfValidatorSchemaFilter('text', new sfValidatorNumber(array('required' => false))),
      'ESTADOTRANSFERENCIA'             => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'MARCA'                           => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'MARCA_ELIMINACION'               => new sfValidatorPass(array('required' => false)),
      'OBS_ELIMINACION'                 => new sfValidatorPass(array('required' => false)),
      'NUMERO_CAJA'                     => new sfValidatorPass(array('required' => false)),
      'NUMERO_IDENTIFICACION'           => new sfValidatorPass(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('unidad_documental_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'UnidadDocumental';
  }

  public function getFields()
  {
    return array(
      'UNIDADDOCUMENTAL_ID'             => 'Number',
      'LOCALIZACIONUNIDADDOCUMENTAL_ID' => 'ForeignKey',
      'REGIONAL_ID'                     => 'ForeignKey',
      'SOPORTEUNIDADDOCUMENTAL_ID'      => 'ForeignKey',
      'ESTADOUNIDADDOCUMENTAL_ID'       => 'ForeignKey',
      'FRECUENCIACONSULTA_ID'           => 'ForeignKey',
      'UNIDADCONSERVADORA_ID'           => 'ForeignKey',
      'SUBSERIE_ID'                     => 'ForeignKey',
      'CODIGO_BARRAS'                   => 'Text',
      'FECHA_APERTURA'                  => 'Date',
      'FECHA_CIERRE'                    => 'Date',
      'TITULO'                          => 'Text',
      'CONTENIDO'                       => 'Text',
      'CREADO_POR_WEB'                  => 'Number',
      'UBICACIONENGESTION'              => 'Text',
      'UBICACIONENCENTRAL'              => 'Text',
      'UBICACIONENHISTORICO'            => 'Text',
      'FOLIOS'                          => 'Number',
      'FECHA_CREACION'                  => 'Date',
      'NOTAS'                           => 'Text',
      'FECHAVENCIMIENTO'                => 'Date',
      'VOLUMEN'                         => 'Number',
      'ESTADOTRANSFERENCIA'             => 'Number',
      'MARCA'                           => 'Number',
      'MARCA_ELIMINACION'               => 'Text',
      'OBS_ELIMINACION'                 => 'Text',
      'NUMERO_CAJA'                     => 'Text',
      'NUMERO_IDENTIFICACION'           => 'Text',
    );
  }
}
