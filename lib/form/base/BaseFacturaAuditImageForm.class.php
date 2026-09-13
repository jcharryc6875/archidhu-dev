<?php

/**
 * FacturaAuditImage form base class.
 *
 * @method FacturaAuditImage getObject() Returns the current form's model object
 *
 * @package    ##PROJECT_NAME##
 * @subpackage form
 * @author     ##AUTHOR_NAME##
 */
abstract class BaseFacturaAuditImageForm extends BaseFormPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'FACTURAAUDITIMAGE_ID' => new sfWidgetFormInputHidden(),
      'USUARIO_ID'           => new sfWidgetFormPropelChoice(array('model' => 'Usuario', 'add_empty' => false)),
      'FACTURA_ID'           => new sfWidgetFormPropelChoice(array('model' => 'Factura', 'add_empty' => false)),
      'FECHA_CREACION'       => new sfWidgetFormDateTime(),
      'FILENAME'             => new sfWidgetFormInputText(),
      'TYPE_ACTION'          => new sfWidgetFormInputText(),
    ));

    $this->setValidators(array(
      'FACTURAAUDITIMAGE_ID' => new sfValidatorChoice(array('choices' => array($this->getObject()->getFacturaauditimageId()), 'empty_value' => $this->getObject()->getFacturaauditimageId(), 'required' => false)),
      'USUARIO_ID'           => new sfValidatorPropelChoice(array('model' => 'Usuario', 'column' => 'USUARIO_ID')),
      'FACTURA_ID'           => new sfValidatorPropelChoice(array('model' => 'Factura', 'column' => 'FACTURA_ID')),
      'FECHA_CREACION'       => new sfValidatorDateTime(array('required' => false)),
      'FILENAME'             => new sfValidatorString(array('max_length' => 100, 'required' => false)),
      'TYPE_ACTION'          => new sfValidatorString(array('max_length' => 10, 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('factura_audit_image[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'FacturaAuditImage';
  }


}
