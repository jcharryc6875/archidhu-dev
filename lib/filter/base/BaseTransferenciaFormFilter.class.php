<?php

/**
 * Transferencia filter form base class.
 *
 * @package    simad
 * @subpackage filter
 * @author     Your name here
 */
abstract class BaseTransferenciaFormFilter extends BaseFormFilterPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'VINCULADA_ID'                => new sfWidgetFormPropelChoice(array('model' => 'Vinculada', 'add_empty' => true)),
      'ESTADOTRANSFERENCIA_ID'      => new sfWidgetFormPropelChoice(array('model' => 'EstadoTransferencia', 'add_empty' => true)),
      'UNIDADDOCUMENTAL_ID'         => new sfWidgetFormPropelChoice(array('model' => 'UnidadDocumental', 'add_empty' => true)),
      'COMENVIADA_ID'               => new sfWidgetFormPropelChoice(array('model' => 'ComEnviada', 'add_empty' => true)),
      'COMINTERNA_ID'               => new sfWidgetFormPropelChoice(array('model' => 'ComInterna', 'add_empty' => true)),
      'ORIGENTRANSFERENCIAID'       => new sfWidgetFormPropelChoice(array('model' => 'OrigenTransferencia', 'add_empty' => true)),
      'COMRECIBIDA_ID'              => new sfWidgetFormPropelChoice(array('model' => 'ComRecibida', 'add_empty' => true)),
      'FACTURA_ID'                  => new sfWidgetFormPropelChoice(array('model' => 'Factura', 'add_empty' => true)),
      'DOCUMENTACION_ID'            => new sfWidgetFormPropelChoice(array('model' => 'Documentacion', 'add_empty' => true)),
      'TIPODOCUMENTAL_ID'           => new sfWidgetFormPropelChoice(array('model' => 'TipoDocumental', 'add_empty' => true)),
      'DESTINOTRANSFERENCIA_ID'     => new sfWidgetFormPropelChoice(array('model' => 'DestinoTransferencia', 'add_empty' => true)),
      'EMAIL_ID'                    => new sfWidgetFormPropelChoice(array('model' => 'Email', 'add_empty' => true)),
      'FECHA_CREACION'              => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate())),
      'FECHA_ACEPTACION'            => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate())),
      'OBSERVACIONES'               => new sfWidgetFormFilterInput(),
      'temp_usuariotranferencia_id' => new sfWidgetFormFilterInput(),
      'MARCA'                       => new sfWidgetFormFilterInput(),
    ));

    $this->setValidators(array(
      'VINCULADA_ID'                => new sfValidatorPropelChoice(array('required' => false, 'model' => 'Vinculada', 'column' => 'VINCULADA_ID')),
      'ESTADOTRANSFERENCIA_ID'      => new sfValidatorPropelChoice(array('required' => false, 'model' => 'EstadoTransferencia', 'column' => 'ESTADOTRANSFERENCIA_ID')),
      'UNIDADDOCUMENTAL_ID'         => new sfValidatorPropelChoice(array('required' => false, 'model' => 'UnidadDocumental', 'column' => 'UNIDADDOCUMENTAL_ID')),
      'COMENVIADA_ID'               => new sfValidatorPropelChoice(array('required' => false, 'model' => 'ComEnviada', 'column' => 'COMENVIADA_ID')),
      'COMINTERNA_ID'               => new sfValidatorPropelChoice(array('required' => false, 'model' => 'ComInterna', 'column' => 'COMINTERNA_ID')),
      'ORIGENTRANSFERENCIAID'       => new sfValidatorPropelChoice(array('required' => false, 'model' => 'OrigenTransferencia', 'column' => 'ORIGENTRANSFERENCIAID')),
      'COMRECIBIDA_ID'              => new sfValidatorPropelChoice(array('required' => false, 'model' => 'ComRecibida', 'column' => 'COMRECIBIDA_ID')),
      'FACTURA_ID'                  => new sfValidatorPropelChoice(array('required' => false, 'model' => 'Factura', 'column' => 'FACTURA_ID')),
      'DOCUMENTACION_ID'            => new sfValidatorPropelChoice(array('required' => false, 'model' => 'Documentacion', 'column' => 'DOCUMENTACION_ID')),
      'TIPODOCUMENTAL_ID'           => new sfValidatorPropelChoice(array('required' => false, 'model' => 'TipoDocumental', 'column' => 'TIPODOCUMENTAL_ID')),
      'DESTINOTRANSFERENCIA_ID'     => new sfValidatorPropelChoice(array('required' => false, 'model' => 'DestinoTransferencia', 'column' => 'DESTINOTRANSFERENCIA_ID')),
      'EMAIL_ID'                    => new sfValidatorPropelChoice(array('required' => false, 'model' => 'Email', 'column' => 'EMAIL_ID')),
      'FECHA_CREACION'              => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDate(array('required' => false)), 'to_date' => new sfValidatorDate(array('required' => false)))),
      'FECHA_ACEPTACION'            => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDate(array('required' => false)), 'to_date' => new sfValidatorDate(array('required' => false)))),
      'OBSERVACIONES'               => new sfValidatorPass(array('required' => false)),
      'temp_usuariotranferencia_id' => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'MARCA'                       => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
    ));

    $this->widgetSchema->setNameFormat('transferencia_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'Transferencia';
  }

  public function getFields()
  {
    return array(
      'TRANSFERENCIA_ID'            => 'Number',
      'VINCULADA_ID'                => 'ForeignKey',
      'ESTADOTRANSFERENCIA_ID'      => 'ForeignKey',
      'UNIDADDOCUMENTAL_ID'         => 'ForeignKey',
      'COMENVIADA_ID'               => 'ForeignKey',
      'COMINTERNA_ID'               => 'ForeignKey',
      'ORIGENTRANSFERENCIAID'       => 'ForeignKey',
      'COMRECIBIDA_ID'              => 'ForeignKey',
      'FACTURA_ID'                  => 'ForeignKey',
      'DOCUMENTACION_ID'            => 'ForeignKey',
      'TIPODOCUMENTAL_ID'           => 'ForeignKey',
      'DESTINOTRANSFERENCIA_ID'     => 'ForeignKey',
      'EMAIL_ID'                    => 'ForeignKey',
      'FECHA_CREACION'              => 'Date',
      'FECHA_ACEPTACION'            => 'Date',
      'OBSERVACIONES'               => 'Text',
      'temp_usuariotranferencia_id' => 'Number',
      'MARCA'                       => 'Number',
    );
  }
}
