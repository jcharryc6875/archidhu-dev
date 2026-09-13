<?php

/**
 * AutcomUsuario filter form base class.
 *
 * @package    simad
 * @subpackage filter
 * @author     Your name here
 */
abstract class BaseAutcomUsuarioFormFilter extends BaseFormFilterPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'USUARIO_ID'          => new sfWidgetFormPropelChoice(array('model' => 'Usuario', 'add_empty' => true)),
      'ROLAUTCOMUSUARIO_ID' => new sfWidgetFormPropelChoice(array('model' => 'RolAutcomUsuario', 'add_empty' => true)),
      'AUTORIZACIONCOM_ID'  => new sfWidgetFormPropelChoice(array('model' => 'AutorizacionCom', 'add_empty' => true)),
    ));

    $this->setValidators(array(
      'USUARIO_ID'          => new sfValidatorPropelChoice(array('required' => false, 'model' => 'Usuario', 'column' => 'USUARIO_ID')),
      'ROLAUTCOMUSUARIO_ID' => new sfValidatorPropelChoice(array('required' => false, 'model' => 'RolAutcomUsuario', 'column' => 'ROLAUTCOMUSUARIO_ID')),
      'AUTORIZACIONCOM_ID'  => new sfValidatorPropelChoice(array('required' => false, 'model' => 'AutorizacionCom', 'column' => 'AUTORIZACIONCOM_ID')),
    ));

    $this->widgetSchema->setNameFormat('autcom_usuario_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'AutcomUsuario';
  }

  public function getFields()
  {
    return array(
      'AUTCOMUSUARIO_ID'    => 'Number',
      'USUARIO_ID'          => 'ForeignKey',
      'ROLAUTCOMUSUARIO_ID' => 'ForeignKey',
      'AUTORIZACIONCOM_ID'  => 'ForeignKey',
    );
  }
}
