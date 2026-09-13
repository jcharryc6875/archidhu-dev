<?php

/**
 * ClUsuario form base class.
 *
 * @method ClUsuario getObject() Returns the current form's model object
 *
 * @package    ##PROJECT_NAME##
 * @subpackage form
 * @author     ##AUTHOR_NAME##
 */
abstract class BaseClUsuarioForm extends BaseFormPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'CL_USUARIO_ID'    => new sfWidgetFormInputHidden(),
      'ROL_CLUSUARIO_ID' => new sfWidgetFormPropelChoice(array('model' => 'RolClusuario', 'add_empty' => false)),
      'USUARIO_ID'       => new sfWidgetFormPropelChoice(array('model' => 'Usuario', 'add_empty' => false)),
      'CLIENTE_ID'       => new sfWidgetFormPropelChoice(array('model' => 'Cliente', 'add_empty' => false)),
    ));

    $this->setValidators(array(
      'CL_USUARIO_ID'    => new sfValidatorChoice(array('choices' => array($this->getObject()->getClUsuarioId()), 'empty_value' => $this->getObject()->getClUsuarioId(), 'required' => false)),
      'ROL_CLUSUARIO_ID' => new sfValidatorPropelChoice(array('model' => 'RolClusuario', 'column' => 'ROL_CLUSUARIO_ID')),
      'USUARIO_ID'       => new sfValidatorPropelChoice(array('model' => 'Usuario', 'column' => 'USUARIO_ID')),
      'CLIENTE_ID'       => new sfValidatorPropelChoice(array('model' => 'Cliente', 'column' => 'CLIENTE_ID')),
    ));

    $this->widgetSchema->setNameFormat('cl_usuario[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'ClUsuario';
  }


}
