<?php

/**
 * AutcomUsuario form base class.
 *
 * @method AutcomUsuario getObject() Returns the current form's model object
 *
 * @package    ##PROJECT_NAME##
 * @subpackage form
 * @author     ##AUTHOR_NAME##
 */
abstract class BaseAutcomUsuarioForm extends BaseFormPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'AUTCOMUSUARIO_ID'    => new sfWidgetFormInputHidden(),
      'USUARIO_ID'          => new sfWidgetFormPropelChoice(array('model' => 'Usuario', 'add_empty' => false)),
      'ROLAUTCOMUSUARIO_ID' => new sfWidgetFormPropelChoice(array('model' => 'RolAutcomUsuario', 'add_empty' => false)),
      'AUTORIZACIONCOM_ID'  => new sfWidgetFormPropelChoice(array('model' => 'AutorizacionCom', 'add_empty' => false)),
    ));

    $this->setValidators(array(
      'AUTCOMUSUARIO_ID'    => new sfValidatorChoice(array('choices' => array($this->getObject()->getAutcomusuarioId()), 'empty_value' => $this->getObject()->getAutcomusuarioId(), 'required' => false)),
      'USUARIO_ID'          => new sfValidatorPropelChoice(array('model' => 'Usuario', 'column' => 'USUARIO_ID')),
      'ROLAUTCOMUSUARIO_ID' => new sfValidatorPropelChoice(array('model' => 'RolAutcomUsuario', 'column' => 'ROLAUTCOMUSUARIO_ID')),
      'AUTORIZACIONCOM_ID'  => new sfValidatorPropelChoice(array('model' => 'AutorizacionCom', 'column' => 'AUTORIZACIONCOM_ID')),
    ));

    $this->widgetSchema->setNameFormat('autcom_usuario[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'AutcomUsuario';
  }


}
