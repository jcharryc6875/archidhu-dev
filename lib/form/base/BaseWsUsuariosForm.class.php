<?php

/**
 * WsUsuarios form base class.
 *
 * @package    form
 * @subpackage ws_usuarios
 * @version    SVN: $Id: sfPropelFormGeneratedTemplate.php 15484 2009-02-13 13:13:51Z fabien $
 */
class BaseWsUsuariosForm extends BaseFormPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'wsusuarios_id' => new sfWidgetFormInputHidden(),
      'usuario'       => new sfWidgetFormInputText(),
      'password'      => new sfWidgetFormInputText(),
      'nombre'        => new sfWidgetFormInputText(),
      'direccion'     => new sfWidgetFormInputText(),
      'telefono'      => new sfWidgetFormInputText(),
      'email'         => new sfWidgetFormInputText(),
    ));

    $this->setValidators(array(
      'wsusuarios_id' => new sfValidatorPropelChoice(array('model' => 'WsUsuarios', 'column' => 'wsusuarios_id', 'required' => false)),
      'usuario'       => new sfValidatorString(array('max_length' => 50, 'required' => false)),
      'password'      => new sfValidatorString(array('max_length' => 100, 'required' => false)),
      'nombre'        => new sfValidatorString(array('max_length' => 100, 'required' => false)),
      'direccion'     => new sfValidatorString(array('max_length' => 200, 'required' => false)),
      'telefono'      => new sfValidatorString(array('max_length' => 20, 'required' => false)),
      'email'         => new sfValidatorString(array('max_length' => 100, 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('ws_usuarios[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'WsUsuarios';
  }


}
