<?php

/**
 * ComrecibidaUsuario form base class.
 *
 * @method ComrecibidaUsuario getObject() Returns the current form's model object
 *
 * @package    ##PROJECT_NAME##
 * @subpackage form
 * @author     ##AUTHOR_NAME##
 */
abstract class BaseComrecibidaUsuarioForm extends BaseFormPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'COMRECIBIDAUSUARIO_ID' => new sfWidgetFormInputHidden(),
      'ROLUSUARIORECIBIDAID'  => new sfWidgetFormPropelChoice(array('model' => 'RolUsuarioRecibida', 'add_empty' => false)),
      'USUARIO_ID'            => new sfWidgetFormPropelChoice(array('model' => 'Usuario', 'add_empty' => false)),
      'ESTADOCOMRECIBIDA_ID'  => new sfWidgetFormPropelChoice(array('model' => 'EstadoComRecibida', 'add_empty' => true)),
      'COMRECIBIDA_ID'        => new sfWidgetFormPropelChoice(array('model' => 'ComRecibida', 'add_empty' => false)),
      'ESTA_ASIGNADA'         => new sfWidgetFormInputText(),
      'CARGOUSUARIO_ID'       => new sfWidgetFormPropelChoice(array('model' => 'CargoUsuario', 'add_empty' => true)),
      'WF_EJECUTADO'          => new sfWidgetFormInputText(),
    ));

    $this->setValidators(array(
      'COMRECIBIDAUSUARIO_ID' => new sfValidatorChoice(array('choices' => array($this->getObject()->getComrecibidausuarioId()), 'empty_value' => $this->getObject()->getComrecibidausuarioId(), 'required' => false)),
      'ROLUSUARIORECIBIDAID'  => new sfValidatorPropelChoice(array('model' => 'RolUsuarioRecibida', 'column' => 'ROLUSUARIORECIBIDAID')),
      'USUARIO_ID'            => new sfValidatorPropelChoice(array('model' => 'Usuario', 'column' => 'USUARIO_ID')),
      'ESTADOCOMRECIBIDA_ID'  => new sfValidatorPropelChoice(array('model' => 'EstadoComRecibida', 'column' => 'ESTADOCOMRECIBIDA_ID', 'required' => false)),
      'COMRECIBIDA_ID'        => new sfValidatorPropelChoice(array('model' => 'ComRecibida', 'column' => 'COMRECIBIDA_ID')),
      'ESTA_ASIGNADA'         => new sfValidatorInteger(array('min' => -128, 'max' => 127, 'required' => false)),
      'CARGOUSUARIO_ID'       => new sfValidatorPropelChoice(array('model' => 'CargoUsuario', 'column' => 'CARGOUSUARIO_ID', 'required' => false)),
      'WF_EJECUTADO'          => new sfValidatorInteger(array('min' => -128, 'max' => 127, 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('comrecibida_usuario[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'ComrecibidaUsuario';
  }


}
