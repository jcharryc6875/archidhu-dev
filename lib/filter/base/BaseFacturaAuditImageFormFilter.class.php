<?php

/**
 * FacturaAuditImage filter form base class.
 *
 * @package    simad
 * @subpackage filter
 * @author     Your name here
 */
abstract class BaseFacturaAuditImageFormFilter extends BaseFormFilterPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'USUARIO_ID'           => new sfWidgetFormPropelChoice(array('model' => 'Usuario', 'add_empty' => true)),
      'FACTURA_ID'           => new sfWidgetFormPropelChoice(array('model' => 'Factura', 'add_empty' => true)),
      'FECHA_CREACION'       => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate())),
      'FILENAME'             => new sfWidgetFormFilterInput(),
      'TYPE_ACTION'          => new sfWidgetFormFilterInput(),
    ));

    $this->setValidators(array(
      'USUARIO_ID'           => new sfValidatorPropelChoice(array('required' => false, 'model' => 'Usuario', 'column' => 'USUARIO_ID')),
      'FACTURA_ID'           => new sfValidatorPropelChoice(array('required' => false, 'model' => 'Factura', 'column' => 'FACTURA_ID')),
      'FECHA_CREACION'       => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDate(array('required' => false)), 'to_date' => new sfValidatorDate(array('required' => false)))),
      'FILENAME'             => new sfValidatorPass(array('required' => false)),
      'TYPE_ACTION'          => new sfValidatorPass(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('factura_audit_image_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'FacturaAuditImage';
  }

  public function getFields()
  {
    return array(
      'FACTURAAUDITIMAGE_ID' => 'Number',
      'USUARIO_ID'           => 'ForeignKey',
      'FACTURA_ID'           => 'ForeignKey',
      'FECHA_CREACION'       => 'Date',
      'FILENAME'             => 'Text',
      'TYPE_ACTION'          => 'Text',
    );
  }
}
