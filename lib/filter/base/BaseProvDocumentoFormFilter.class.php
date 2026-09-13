<?php

/**
 * ProvDocumento filter form base class.
 *
 * @package    simad
 * @subpackage filter
 * @author     Your name here
 */
abstract class BaseProvDocumentoFormFilter extends BaseFormFilterPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'PROV_PERIODO_VALIDEZ_ID' => new sfWidgetFormPropelChoice(array('model' => 'ProvPeriodoValidez', 'add_empty' => true)),
      'PROV_LISTA_DOCS_ID'      => new sfWidgetFormPropelChoice(array('model' => 'ProvListaDocs', 'add_empty' => true)),
      'PROV_ESTADO_DOC_ID'      => new sfWidgetFormPropelChoice(array('model' => 'ProvEstadoDoc', 'add_empty' => true)),
      'FECHA_CREACION'          => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate())),
      'OBSERVACIONES'           => new sfWidgetFormFilterInput(),
      'RUTA'                    => new sfWidgetFormFilterInput(),
      'FECHA_RECIBIDO'          => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate())),
    ));

    $this->setValidators(array(
      'PROV_PERIODO_VALIDEZ_ID' => new sfValidatorPropelChoice(array('required' => false, 'model' => 'ProvPeriodoValidez', 'column' => 'PROV_PERIODO_VALIDEZ_ID')),
      'PROV_LISTA_DOCS_ID'      => new sfValidatorPropelChoice(array('required' => false, 'model' => 'ProvListaDocs', 'column' => 'PROV_LISTA_DOCS_ID')),
      'PROV_ESTADO_DOC_ID'      => new sfValidatorPropelChoice(array('required' => false, 'model' => 'ProvEstadoDoc', 'column' => 'PROV_ESTADO_DOC_ID')),
      'FECHA_CREACION'          => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDate(array('required' => false)), 'to_date' => new sfValidatorDate(array('required' => false)))),
      'OBSERVACIONES'           => new sfValidatorPass(array('required' => false)),
      'RUTA'                    => new sfValidatorPass(array('required' => false)),
      'FECHA_RECIBIDO'          => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDate(array('required' => false)), 'to_date' => new sfValidatorDate(array('required' => false)))),
    ));

    $this->widgetSchema->setNameFormat('prov_documento_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'ProvDocumento';
  }

  public function getFields()
  {
    return array(
      'PROV_DOCUMENTO_ID'       => 'Number',
      'PROV_PERIODO_VALIDEZ_ID' => 'ForeignKey',
      'PROV_LISTA_DOCS_ID'      => 'ForeignKey',
      'PROV_ESTADO_DOC_ID'      => 'ForeignKey',
      'FECHA_CREACION'          => 'Date',
      'OBSERVACIONES'           => 'Text',
      'RUTA'                    => 'Text',
      'FECHA_RECIBIDO'          => 'Date',
    );
  }
}
