<?php

/**
 * CominternaUsuario form base class.
 *
 * @method CominternaUsuario getObject() Returns the current form's model object
 *
 * @package    ##PROJECT_NAME##
 * @subpackage form
 * @author     ##AUTHOR_NAME##
 */
abstract class BaseCominternaUsuarioForm extends BaseFormPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'COMINTERNAUSUARIO_ID'    => new sfWidgetFormInputHidden(),
      'ESTADOCOMINTERNA_ID'     => new sfWidgetFormPropelChoice(array('model' => 'EstadoComInterna', 'add_empty' => true)),
      'COMINTERNA_ID'           => new sfWidgetFormPropelChoice(array('model' => 'ComInterna', 'add_empty' => false)),
      'ROLUSUARIOCOMINTERNA_ID' => new sfWidgetFormPropelChoice(array('model' => 'RolusuarioCominterna', 'add_empty' => false)),
      'CARGOUSUARIO_ID'         => new sfWidgetFormPropelChoice(array('model' => 'CargoUsuario', 'add_empty' => false)),
      'USUARIO_ID'              => new sfWidgetFormPropelChoice(array('model' => 'Usuario', 'add_empty' => false)),
      'WF_EJECUTADO'            => new sfWidgetFormInputText(),
    ));

    $this->setValidators(array(
      'COMINTERNAUSUARIO_ID'    => new sfValidatorChoice(array('choices' => array($this->getObject()->getCominternausuarioId()), 'empty_value' => $this->getObject()->getCominternausuarioId(), 'required' => false)),
      'ESTADOCOMINTERNA_ID'     => new sfValidatorPropelChoice(array('model' => 'EstadoComInterna', 'column' => 'ESTADOCOMINTERNA_ID', 'required' => false)),
      'COMINTERNA_ID'           => new sfValidatorPropelChoice(array('model' => 'ComInterna', 'column' => 'COMINTERNA_ID')),
      'ROLUSUARIOCOMINTERNA_ID' => new sfValidatorPropelChoice(array('model' => 'RolusuarioCominterna', 'column' => 'ROLUSUARIOCOMINTERNA_ID')),
      'CARGOUSUARIO_ID'         => new sfValidatorPropelChoice(array('model' => 'CargoUsuario', 'column' => 'CARGOUSUARIO_ID')),
      'USUARIO_ID'              => new sfValidatorPropelChoice(array('model' => 'Usuario', 'column' => 'USUARIO_ID')),
      'WF_EJECUTADO'            => new sfValidatorInteger(array('min' => -128, 'max' => 127, 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('cominterna_usuario[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'CominternaUsuario';
  }


}
