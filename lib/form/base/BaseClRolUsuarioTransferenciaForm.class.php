<?php

/**
 * ClRolUsuarioTransferencia form base class.
 *
 * @method ClRolUsuarioTransferencia getObject() Returns the current form's model object
 *
 * @package    ##PROJECT_NAME##
 * @subpackage form
 * @author     ##AUTHOR_NAME##
 */
abstract class BaseClRolUsuarioTransferenciaForm extends BaseFormPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'CLROLUSUARIOTRANSFERENCIA_ID' => new sfWidgetFormInputHidden(),
      'DESCRIPCION'                  => new sfWidgetFormInputText(),
    ));

    $this->setValidators(array(
      'CLROLUSUARIOTRANSFERENCIA_ID' => new sfValidatorChoice(array('choices' => array($this->getObject()->getClrolusuariotransferenciaId()), 'empty_value' => $this->getObject()->getClrolusuariotransferenciaId(), 'required' => false)),
      'DESCRIPCION'                  => new sfValidatorString(array('max_length' => 250, 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('cl_rol_usuario_transferencia[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'ClRolUsuarioTransferencia';
  }


}
