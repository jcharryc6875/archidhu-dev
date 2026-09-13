<?php

/**
 * RolPorUsuario filter form base class.
 *
 * @package    simad
 * @subpackage filter
 * @author     Your name here
 */
abstract class BaseRolPorUsuarioFormFilter extends BaseFormFilterPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'USUARIO_ID'       => new sfWidgetFormPropelChoice(array('model' => 'Usuario', 'add_empty' => true)),
      'ROL_ID'           => new sfWidgetFormPropelChoice(array('model' => 'Rol', 'add_empty' => true)),
    ));

    $this->setValidators(array(
      'USUARIO_ID'       => new sfValidatorPropelChoice(array('required' => false, 'model' => 'Usuario', 'column' => 'USUARIO_ID')),
      'ROL_ID'           => new sfValidatorPropelChoice(array('required' => false, 'model' => 'Rol', 'column' => 'ROL_ID')),
    ));

    $this->widgetSchema->setNameFormat('rol_por_usuario_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'RolPorUsuario';
  }

  public function getFields()
  {
    return array(
      'ROLPORUSUARIO_ID' => 'Number',
      'USUARIO_ID'       => 'ForeignKey',
      'ROL_ID'           => 'ForeignKey',
    );
  }
}
