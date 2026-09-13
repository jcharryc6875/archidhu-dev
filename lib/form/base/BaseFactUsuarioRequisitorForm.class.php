<?php

/**
 * FactUsuarioRequisitor form base class.
 *
 * @method FactUsuarioRequisitor getObject() Returns the current form's model object
 *
 * @package    ##PROJECT_NAME##
 * @subpackage form
 * @author     ##AUTHOR_NAME##
 */
abstract class BaseFactUsuarioRequisitorForm extends BaseFormPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'FACTUSUARIOREQUISITOR_ID'     => new sfWidgetFormInputHidden(),
      'USUARIO_ID'                   => new sfWidgetFormPropelChoice(array('model' => 'Usuario', 'add_empty' => true)),
      'FACTURAVITACORA_ID'           => new sfWidgetFormPropelChoice(array('model' => 'FacturaVitacora', 'add_empty' => true)),
      'FACTURAVITACORAROLUSUARIO_ID' => new sfWidgetFormPropelChoice(array('model' => 'FacturaVitacoraRolUsuario', 'add_empty' => true)),
    ));

    $this->setValidators(array(
      'FACTUSUARIOREQUISITOR_ID'     => new sfValidatorChoice(array('choices' => array($this->getObject()->getFactusuariorequisitorId()), 'empty_value' => $this->getObject()->getFactusuariorequisitorId(), 'required' => false)),
      'USUARIO_ID'                   => new sfValidatorPropelChoice(array('model' => 'Usuario', 'column' => 'USUARIO_ID', 'required' => false)),
      'FACTURAVITACORA_ID'           => new sfValidatorPropelChoice(array('model' => 'FacturaVitacora', 'column' => 'FACTURAVITACORA_ID', 'required' => false)),
      'FACTURAVITACORAROLUSUARIO_ID' => new sfValidatorPropelChoice(array('model' => 'FacturaVitacoraRolUsuario', 'column' => 'FACTURAVITACORAROLUSUARIO_ID', 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('fact_usuario_requisitor[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'FactUsuarioRequisitor';
  }


}
