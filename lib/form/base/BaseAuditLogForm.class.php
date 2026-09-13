<?php

/**
 * AuditLog form base class.
 *
 * @method AuditLog getObject() Returns the current form's model object
 *
 * @package    ##PROJECT_NAME##
 * @subpackage form
 * @author     ##AUTHOR_NAME##
 */
abstract class BaseAuditLogForm extends BaseFormPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'AUDIT_LOG_ID'     => new sfWidgetFormInputHidden(),
      'USUARIO_ID'       => new sfWidgetFormPropelChoice(array('model' => 'Usuario', 'add_empty' => false)),
      'MODULO_ID'        => new sfWidgetFormPropelChoice(array('model' => 'Modulo', 'add_empty' => false)),
      'CAMPOS'           => new sfWidgetFormTextarea(),
      'VALOR_ANTERIOR'   => new sfWidgetFormTextarea(),
      'VALOR_NUEVO'      => new sfWidgetFormTextarea(),
      'FECHA_CREACION'   => new sfWidgetFormDateTime(),
      'CODIGO_PRINCIPAL' => new sfWidgetFormInputText(),
    ));

    $this->setValidators(array(
      'AUDIT_LOG_ID'     => new sfValidatorChoice(array('choices' => array($this->getObject()->getAuditLogId()), 'empty_value' => $this->getObject()->getAuditLogId(), 'required' => false)),
      'USUARIO_ID'       => new sfValidatorPropelChoice(array('model' => 'Usuario', 'column' => 'USUARIO_ID')),
      'MODULO_ID'        => new sfValidatorPropelChoice(array('model' => 'Modulo', 'column' => 'MODULO_ID')),
      'CAMPOS'           => new sfValidatorString(array('required' => false)),
      'VALOR_ANTERIOR'   => new sfValidatorString(array('required' => false)),
      'VALOR_NUEVO'      => new sfValidatorString(array('required' => false)),
      'FECHA_CREACION'   => new sfValidatorDateTime(array('required' => false)),
      'CODIGO_PRINCIPAL' => new sfValidatorString(array('max_length' => 100, 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('audit_log[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'AuditLog';
  }


}
