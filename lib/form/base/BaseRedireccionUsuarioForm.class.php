<?php

/**
 * RedireccionUsuario form base class.
 *
 * @method RedireccionUsuario getObject() Returns the current form's model object
 *
 * @package    ##PROJECT_NAME##
 * @subpackage form
 * @author     ##AUTHOR_NAME##
 */
abstract class BaseRedireccionUsuarioForm extends BaseFormPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'REDIRECCIONUSUARIO_ID' => new sfWidgetFormInputHidden(),
      'REDIRECCION_ID'        => new sfWidgetFormPropelChoice(array('model' => 'Redireccion', 'add_empty' => false)),
      'ROLUSUREDIRECCION_ID'  => new sfWidgetFormPropelChoice(array('model' => 'RolUsuRedireccion', 'add_empty' => false)),
      'USUARIO_ID'            => new sfWidgetFormPropelChoice(array('model' => 'Usuario', 'add_empty' => false)),
    ));

    $this->setValidators(array(
      'REDIRECCIONUSUARIO_ID' => new sfValidatorChoice(array('choices' => array($this->getObject()->getRedireccionusuarioId()), 'empty_value' => $this->getObject()->getRedireccionusuarioId(), 'required' => false)),
      'REDIRECCION_ID'        => new sfValidatorPropelChoice(array('model' => 'Redireccion', 'column' => 'REDIRECCION_ID')),
      'ROLUSUREDIRECCION_ID'  => new sfValidatorPropelChoice(array('model' => 'RolUsuRedireccion', 'column' => 'ROLUSUREDIRECCION_ID')),
      'USUARIO_ID'            => new sfValidatorPropelChoice(array('model' => 'Usuario', 'column' => 'USUARIO_ID')),
    ));

    $this->widgetSchema->setNameFormat('redireccion_usuario[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'RedireccionUsuario';
  }


}
