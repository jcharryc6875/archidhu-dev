<?php

/**
 * FacturaVitacoraRolUsuario form base class.
 *
 * @method FacturaVitacoraRolUsuario getObject() Returns the current form's model object
 *
 * @package    ##PROJECT_NAME##
 * @subpackage form
 * @author     ##AUTHOR_NAME##
 */
abstract class BaseFacturaVitacoraRolUsuarioForm extends BaseFormPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'FACTURAVITACORAROLUSUARIO_ID' => new sfWidgetFormInputHidden(),
      'DESCRIPCION'                  => new sfWidgetFormInputText(),
    ));

    $this->setValidators(array(
      'FACTURAVITACORAROLUSUARIO_ID' => new sfValidatorChoice(array('choices' => array($this->getObject()->getFacturavitacorarolusuarioId()), 'empty_value' => $this->getObject()->getFacturavitacorarolusuarioId(), 'required' => false)),
      'DESCRIPCION'                  => new sfValidatorString(array('max_length' => 250, 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('factura_vitacora_rol_usuario[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'FacturaVitacoraRolUsuario';
  }


}
