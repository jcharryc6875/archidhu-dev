<?php

/**
 * RedireccionUsuario filter form base class.
 *
 * @package    simad
 * @subpackage filter
 * @author     Your name here
 */
abstract class BaseRedireccionUsuarioFormFilter extends BaseFormFilterPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'REDIRECCION_ID'        => new sfWidgetFormPropelChoice(array('model' => 'Redireccion', 'add_empty' => true)),
      'ROLUSUREDIRECCION_ID'  => new sfWidgetFormPropelChoice(array('model' => 'RolUsuRedireccion', 'add_empty' => true)),
      'USUARIO_ID'            => new sfWidgetFormPropelChoice(array('model' => 'Usuario', 'add_empty' => true)),
    ));

    $this->setValidators(array(
      'REDIRECCION_ID'        => new sfValidatorPropelChoice(array('required' => false, 'model' => 'Redireccion', 'column' => 'REDIRECCION_ID')),
      'ROLUSUREDIRECCION_ID'  => new sfValidatorPropelChoice(array('required' => false, 'model' => 'RolUsuRedireccion', 'column' => 'ROLUSUREDIRECCION_ID')),
      'USUARIO_ID'            => new sfValidatorPropelChoice(array('required' => false, 'model' => 'Usuario', 'column' => 'USUARIO_ID')),
    ));

    $this->widgetSchema->setNameFormat('redireccion_usuario_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'RedireccionUsuario';
  }

  public function getFields()
  {
    return array(
      'REDIRECCIONUSUARIO_ID' => 'Number',
      'REDIRECCION_ID'        => 'ForeignKey',
      'ROLUSUREDIRECCION_ID'  => 'ForeignKey',
      'USUARIO_ID'            => 'ForeignKey',
    );
  }
}
