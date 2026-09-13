<?php

/**
 * UsuarioPrivilegio filter form base class.
 *
 * @package    simad
 * @subpackage filter
 * @author     Your name here
 */
abstract class BaseUsuarioPrivilegioFormFilter extends BaseFormFilterPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'FORMA_ID'             => new sfWidgetFormPropelChoice(array('model' => 'Forma', 'add_empty' => true)),
      'USUARIO_ID'           => new sfWidgetFormPropelChoice(array('model' => 'Usuario', 'add_empty' => true)),
    ));

    $this->setValidators(array(
      'FORMA_ID'             => new sfValidatorPropelChoice(array('required' => false, 'model' => 'Forma', 'column' => 'FORMA_ID')),
      'USUARIO_ID'           => new sfValidatorPropelChoice(array('required' => false, 'model' => 'Usuario', 'column' => 'USUARIO_ID')),
    ));

    $this->widgetSchema->setNameFormat('usuario_privilegio_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'UsuarioPrivilegio';
  }

  public function getFields()
  {
    return array(
      'USUARIOPRIVILEGIO_ID' => 'Number',
      'FORMA_ID'             => 'ForeignKey',
      'USUARIO_ID'           => 'ForeignKey',
    );
  }
}
