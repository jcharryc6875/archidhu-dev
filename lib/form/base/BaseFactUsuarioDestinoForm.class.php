<?php

/**
 * FactUsuarioDestino form base class.
 *
 * @method FactUsuarioDestino getObject() Returns the current form's model object
 *
 * @package    ##PROJECT_NAME##
 * @subpackage form
 * @author     ##AUTHOR_NAME##
 */
abstract class BaseFactUsuarioDestinoForm extends BaseFormPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'FACTUSUARIODESTINO_ID'        => new sfWidgetFormInputHidden(),
      'USUARIO_ID'                   => new sfWidgetFormPropelChoice(array('model' => 'Usuario', 'add_empty' => true)),
      'FACTURAVITACORA_ID'           => new sfWidgetFormPropelChoice(array('model' => 'FacturaVitacora', 'add_empty' => true)),
      'FACTURAVITACORAROLUSUARIO_ID' => new sfWidgetFormPropelChoice(array('model' => 'FacturaVitacoraRolUsuario', 'add_empty' => true)),
    ));

    $this->setValidators(array(
      'FACTUSUARIODESTINO_ID'        => new sfValidatorChoice(array('choices' => array($this->getObject()->getFactusuariodestinoId()), 'empty_value' => $this->getObject()->getFactusuariodestinoId(), 'required' => false)),
      'USUARIO_ID'                   => new sfValidatorPropelChoice(array('model' => 'Usuario', 'column' => 'USUARIO_ID', 'required' => false)),
      'FACTURAVITACORA_ID'           => new sfValidatorPropelChoice(array('model' => 'FacturaVitacora', 'column' => 'FACTURAVITACORA_ID', 'required' => false)),
      'FACTURAVITACORAROLUSUARIO_ID' => new sfValidatorPropelChoice(array('model' => 'FacturaVitacoraRolUsuario', 'column' => 'FACTURAVITACORAROLUSUARIO_ID', 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('fact_usuario_destino[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'FactUsuarioDestino';
  }


}
