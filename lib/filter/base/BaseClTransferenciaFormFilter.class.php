<?php

/**
 * ClTransferencia filter form base class.
 *
 * @package    simad
 * @subpackage filter
 * @author     Your name here
 */
abstract class BaseClTransferenciaFormFilter extends BaseFormFilterPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'CLESTADOTRANSFERENCIA_ID'  => new sfWidgetFormPropelChoice(array('model' => 'ClEstadoTransferencia', 'add_empty' => true)),
      'COMINTERNA_ID'             => new sfWidgetFormPropelChoice(array('model' => 'ComInterna', 'add_empty' => true)),
      'COMRECIBIDA_ID'            => new sfWidgetFormPropelChoice(array('model' => 'ComRecibida', 'add_empty' => true)),
      'CLIENTE_ID'                => new sfWidgetFormPropelChoice(array('model' => 'Cliente', 'add_empty' => true)),
      'COMENVIADA_ID'             => new sfWidgetFormPropelChoice(array('model' => 'ComEnviada', 'add_empty' => true)),
      'CLORIGENTRANSFERENCIA_ID'  => new sfWidgetFormPropelChoice(array('model' => 'ClOrigenTransferencia', 'add_empty' => true)),
      'CLDESTINOTRANSFERENCIA_ID' => new sfWidgetFormPropelChoice(array('model' => 'ClDestinoTransferencia', 'add_empty' => true)),
      'TIPODOCUMENTAL_ID'         => new sfWidgetFormPropelChoice(array('model' => 'TipoDocumental', 'add_empty' => true)),
      'DOCUMENTACION_ID'          => new sfWidgetFormPropelChoice(array('model' => 'Documentacion', 'add_empty' => true)),
      'FECHA_ACEPTACION'          => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate())),
      'FECHA_CREACION'            => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate())),
    ));

    $this->setValidators(array(
      'CLESTADOTRANSFERENCIA_ID'  => new sfValidatorPropelChoice(array('required' => false, 'model' => 'ClEstadoTransferencia', 'column' => 'CLESTADOTRANSFERENCIA_ID')),
      'COMINTERNA_ID'             => new sfValidatorPropelChoice(array('required' => false, 'model' => 'ComInterna', 'column' => 'COMINTERNA_ID')),
      'COMRECIBIDA_ID'            => new sfValidatorPropelChoice(array('required' => false, 'model' => 'ComRecibida', 'column' => 'COMRECIBIDA_ID')),
      'CLIENTE_ID'                => new sfValidatorPropelChoice(array('required' => false, 'model' => 'Cliente', 'column' => 'CLIENTE_ID')),
      'COMENVIADA_ID'             => new sfValidatorPropelChoice(array('required' => false, 'model' => 'ComEnviada', 'column' => 'COMENVIADA_ID')),
      'CLORIGENTRANSFERENCIA_ID'  => new sfValidatorPropelChoice(array('required' => false, 'model' => 'ClOrigenTransferencia', 'column' => 'CLORIGENTRANSFERENCIA_ID')),
      'CLDESTINOTRANSFERENCIA_ID' => new sfValidatorPropelChoice(array('required' => false, 'model' => 'ClDestinoTransferencia', 'column' => 'CLDESTINOTRANSFERENCIA_ID')),
      'TIPODOCUMENTAL_ID'         => new sfValidatorPropelChoice(array('required' => false, 'model' => 'TipoDocumental', 'column' => 'TIPODOCUMENTAL_ID')),
      'DOCUMENTACION_ID'          => new sfValidatorPropelChoice(array('required' => false, 'model' => 'Documentacion', 'column' => 'DOCUMENTACION_ID')),
      'FECHA_ACEPTACION'          => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDate(array('required' => false)), 'to_date' => new sfValidatorDate(array('required' => false)))),
      'FECHA_CREACION'            => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDate(array('required' => false)), 'to_date' => new sfValidatorDate(array('required' => false)))),
    ));

    $this->widgetSchema->setNameFormat('cl_transferencia_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'ClTransferencia';
  }

  public function getFields()
  {
    return array(
      'CLTRANSFERENCIA_ID'        => 'Number',
      'CLESTADOTRANSFERENCIA_ID'  => 'ForeignKey',
      'COMINTERNA_ID'             => 'ForeignKey',
      'COMRECIBIDA_ID'            => 'ForeignKey',
      'CLIENTE_ID'                => 'ForeignKey',
      'COMENVIADA_ID'             => 'ForeignKey',
      'CLORIGENTRANSFERENCIA_ID'  => 'ForeignKey',
      'CLDESTINOTRANSFERENCIA_ID' => 'ForeignKey',
      'TIPODOCUMENTAL_ID'         => 'ForeignKey',
      'DOCUMENTACION_ID'          => 'ForeignKey',
      'FECHA_ACEPTACION'          => 'Date',
      'FECHA_CREACION'            => 'Date',
    );
  }
}
