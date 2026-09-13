<?php

/**
 * ClUsuario filter form base class.
 *
 * @package    simad
 * @subpackage filter
 * @author     Your name here
 */
abstract class BaseClUsuarioFormFilter extends BaseFormFilterPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'ROL_CLUSUARIO_ID' => new sfWidgetFormPropelChoice(array('model' => 'RolClusuario', 'add_empty' => true)),
      'USUARIO_ID'       => new sfWidgetFormPropelChoice(array('model' => 'Usuario', 'add_empty' => true)),
      'CLIENTE_ID'       => new sfWidgetFormPropelChoice(array('model' => 'Cliente', 'add_empty' => true)),
    ));

    $this->setValidators(array(
      'ROL_CLUSUARIO_ID' => new sfValidatorPropelChoice(array('required' => false, 'model' => 'RolClusuario', 'column' => 'ROL_CLUSUARIO_ID')),
      'USUARIO_ID'       => new sfValidatorPropelChoice(array('required' => false, 'model' => 'Usuario', 'column' => 'USUARIO_ID')),
      'CLIENTE_ID'       => new sfValidatorPropelChoice(array('required' => false, 'model' => 'Cliente', 'column' => 'CLIENTE_ID')),
    ));

    $this->widgetSchema->setNameFormat('cl_usuario_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'ClUsuario';
  }

  public function getFields()
  {
    return array(
      'CL_USUARIO_ID'    => 'Number',
      'ROL_CLUSUARIO_ID' => 'ForeignKey',
      'USUARIO_ID'       => 'ForeignKey',
      'CLIENTE_ID'       => 'ForeignKey',
    );
  }
}
