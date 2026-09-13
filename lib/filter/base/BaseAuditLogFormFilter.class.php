<?php

/**
 * AuditLog filter form base class.
 *
 * @package    simad
 * @subpackage filter
 * @author     Your name here
 */
abstract class BaseAuditLogFormFilter extends BaseFormFilterPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'USUARIO_ID'       => new sfWidgetFormPropelChoice(array('model' => 'Usuario', 'add_empty' => true)),
      'MODULO_ID'        => new sfWidgetFormPropelChoice(array('model' => 'Modulo', 'add_empty' => true)),
      'CAMPOS'           => new sfWidgetFormFilterInput(),
      'VALOR_ANTERIOR'   => new sfWidgetFormFilterInput(),
      'VALOR_NUEVO'      => new sfWidgetFormFilterInput(),
      'FECHA_CREACION'   => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate())),
      'CODIGO_PRINCIPAL' => new sfWidgetFormFilterInput(),
    ));

    $this->setValidators(array(
      'USUARIO_ID'       => new sfValidatorPropelChoice(array('required' => false, 'model' => 'Usuario', 'column' => 'USUARIO_ID')),
      'MODULO_ID'        => new sfValidatorPropelChoice(array('required' => false, 'model' => 'Modulo', 'column' => 'MODULO_ID')),
      'CAMPOS'           => new sfValidatorPass(array('required' => false)),
      'VALOR_ANTERIOR'   => new sfValidatorPass(array('required' => false)),
      'VALOR_NUEVO'      => new sfValidatorPass(array('required' => false)),
      'FECHA_CREACION'   => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDate(array('required' => false)), 'to_date' => new sfValidatorDate(array('required' => false)))),
      'CODIGO_PRINCIPAL' => new sfValidatorPass(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('audit_log_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'AuditLog';
  }

  public function getFields()
  {
    return array(
      'AUDIT_LOG_ID'     => 'Number',
      'USUARIO_ID'       => 'ForeignKey',
      'MODULO_ID'        => 'ForeignKey',
      'CAMPOS'           => 'Text',
      'VALOR_ANTERIOR'   => 'Text',
      'VALOR_NUEVO'      => 'Text',
      'FECHA_CREACION'   => 'Date',
      'CODIGO_PRINCIPAL' => 'Text',
    );
  }
}
