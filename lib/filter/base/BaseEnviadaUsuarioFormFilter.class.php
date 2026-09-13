<?php

/**
 * EnviadaUsuario filter form base class.
 *
 * @package    simad
 * @subpackage filter
 * @author     Your name here
 */
abstract class BaseEnviadaUsuarioFormFilter extends BaseFormFilterPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'ESTADOCOMENVIADA_ID' => new sfWidgetFormPropelChoice(array('model' => 'EstadoComEnviada', 'add_empty' => true)),
      'USUARIO_ID'          => new sfWidgetFormPropelChoice(array('model' => 'Usuario', 'add_empty' => true)),
      'COMENVIADA_ID'       => new sfWidgetFormPropelChoice(array('model' => 'ComEnviada', 'add_empty' => true)),
      'ROLUSCOMENVIADA_ID'  => new sfWidgetFormPropelChoice(array('model' => 'RolusComenviada', 'add_empty' => true)),
      'CARGOUSUARIO_ID'     => new sfWidgetFormPropelChoice(array('model' => 'CargoUsuario', 'add_empty' => true)),
    ));

    $this->setValidators(array(
      'ESTADOCOMENVIADA_ID' => new sfValidatorPropelChoice(array('required' => false, 'model' => 'EstadoComEnviada', 'column' => 'ESTADOCOMENVIADA_ID')),
      'USUARIO_ID'          => new sfValidatorPropelChoice(array('required' => false, 'model' => 'Usuario', 'column' => 'USUARIO_ID')),
      'COMENVIADA_ID'       => new sfValidatorPropelChoice(array('required' => false, 'model' => 'ComEnviada', 'column' => 'COMENVIADA_ID')),
      'ROLUSCOMENVIADA_ID'  => new sfValidatorPropelChoice(array('required' => false, 'model' => 'RolusComenviada', 'column' => 'ROLUSCOMENVIADA_ID')),
      'CARGOUSUARIO_ID'     => new sfValidatorPropelChoice(array('required' => false, 'model' => 'CargoUsuario', 'column' => 'CARGOUSUARIO_ID')),
    ));

    $this->widgetSchema->setNameFormat('enviada_usuario_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'EnviadaUsuario';
  }

  public function getFields()
  {
    return array(
      'ENVIADAUSUARIO_ID'   => 'Number',
      'ESTADOCOMENVIADA_ID' => 'ForeignKey',
      'USUARIO_ID'          => 'ForeignKey',
      'COMENVIADA_ID'       => 'ForeignKey',
      'ROLUSCOMENVIADA_ID'  => 'ForeignKey',
      'CARGOUSUARIO_ID'     => 'ForeignKey',
    );
  }
}
