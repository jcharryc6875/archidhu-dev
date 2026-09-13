<?php

/**
 * FacturaVitacoraUsuario form base class.
 *
 * @method FacturaVitacoraUsuario getObject() Returns the current form's model object
 *
 * @package    ##PROJECT_NAME##
 * @subpackage form
 * @author     ##AUTHOR_NAME##
 */
abstract class BaseFacturaVitacoraUsuarioForm extends BaseFormPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'FACTURAVITACORAUSUARIO_ID'    => new sfWidgetFormInputHidden(),
      'FACTURAVITACORAROLUSUARIO_ID' => new sfWidgetFormPropelChoice(array('model' => 'FacturaVitacoraRolUsuario', 'add_empty' => false)),
      'FACTURAVITACORA_ID'           => new sfWidgetFormPropelChoice(array('model' => 'FacturaVitacora', 'add_empty' => false)),
      'USUARIO_ID'                   => new sfWidgetFormPropelChoice(array('model' => 'Usuario', 'add_empty' => false)),
    ));

    $this->setValidators(array(
      'FACTURAVITACORAUSUARIO_ID'    => new sfValidatorChoice(array('choices' => array($this->getObject()->getFacturavitacorausuarioId()), 'empty_value' => $this->getObject()->getFacturavitacorausuarioId(), 'required' => false)),
      'FACTURAVITACORAROLUSUARIO_ID' => new sfValidatorPropelChoice(array('model' => 'FacturaVitacoraRolUsuario', 'column' => 'FACTURAVITACORAROLUSUARIO_ID')),
      'FACTURAVITACORA_ID'           => new sfValidatorPropelChoice(array('model' => 'FacturaVitacora', 'column' => 'FACTURAVITACORA_ID')),
      'USUARIO_ID'                   => new sfValidatorPropelChoice(array('model' => 'Usuario', 'column' => 'USUARIO_ID')),
    ));

    $this->widgetSchema->setNameFormat('factura_vitacora_usuario[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'FacturaVitacoraUsuario';
  }


}
