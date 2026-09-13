<?php

/**
 * EnviadaUsuario form base class.
 *
 * @method EnviadaUsuario getObject() Returns the current form's model object
 *
 * @package    ##PROJECT_NAME##
 * @subpackage form
 * @author     ##AUTHOR_NAME##
 */
abstract class BaseEnviadaUsuarioForm extends BaseFormPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'ENVIADAUSUARIO_ID'   => new sfWidgetFormInputHidden(),
      'ESTADOCOMENVIADA_ID' => new sfWidgetFormPropelChoice(array('model' => 'EstadoComEnviada', 'add_empty' => true)),
      'USUARIO_ID'          => new sfWidgetFormPropelChoice(array('model' => 'Usuario', 'add_empty' => false)),
      'COMENVIADA_ID'       => new sfWidgetFormPropelChoice(array('model' => 'ComEnviada', 'add_empty' => false)),
      'ROLUSCOMENVIADA_ID'  => new sfWidgetFormPropelChoice(array('model' => 'RolusComenviada', 'add_empty' => false)),
      'CARGOUSUARIO_ID'     => new sfWidgetFormPropelChoice(array('model' => 'CargoUsuario', 'add_empty' => true)),
    ));

    $this->setValidators(array(
      'ENVIADAUSUARIO_ID'   => new sfValidatorChoice(array('choices' => array($this->getObject()->getEnviadausuarioId()), 'empty_value' => $this->getObject()->getEnviadausuarioId(), 'required' => false)),
      'ESTADOCOMENVIADA_ID' => new sfValidatorPropelChoice(array('model' => 'EstadoComEnviada', 'column' => 'ESTADOCOMENVIADA_ID', 'required' => false)),
      'USUARIO_ID'          => new sfValidatorPropelChoice(array('model' => 'Usuario', 'column' => 'USUARIO_ID')),
      'COMENVIADA_ID'       => new sfValidatorPropelChoice(array('model' => 'ComEnviada', 'column' => 'COMENVIADA_ID')),
      'ROLUSCOMENVIADA_ID'  => new sfValidatorPropelChoice(array('model' => 'RolusComenviada', 'column' => 'ROLUSCOMENVIADA_ID')),
      'CARGOUSUARIO_ID'     => new sfValidatorPropelChoice(array('model' => 'CargoUsuario', 'column' => 'CARGOUSUARIO_ID', 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('enviada_usuario[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'EnviadaUsuario';
  }


}
