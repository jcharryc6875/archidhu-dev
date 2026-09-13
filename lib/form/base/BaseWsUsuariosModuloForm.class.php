<?php

/**
 * WsUsuariosModulo form base class.
 *
 * @package    form
 * @subpackage ws_usuarios_modulo
 * @version    SVN: $Id: sfPropelFormGeneratedTemplate.php 15484 2009-02-13 13:13:51Z fabien $
 */
class BaseWsUsuariosModuloForm extends BaseFormPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'wsusuariosmodulo_id' => new sfWidgetFormInputHidden(),
      'modulo_id'           => new sfWidgetFormPropelSelect(array('model' => 'Modulo', 'add_empty' => false)),
      'wsusuarios_id'       => new sfWidgetFormPropelSelect(array('model' => 'WsUsuarios', 'add_empty' => false)),
    ));

    $this->setValidators(array(
      'wsusuariosmodulo_id' => new sfValidatorPropelChoice(array('model' => 'WsUsuariosModulo', 'column' => 'wsusuariosmodulo_id', 'required' => false)),
      'modulo_id'           => new sfValidatorPropelChoice(array('model' => 'Modulo', 'column' => 'modulo_id')),
      'wsusuarios_id'       => new sfValidatorPropelChoice(array('model' => 'WsUsuarios', 'column' => 'wsusuarios_id')),
    ));

    $this->widgetSchema->setNameFormat('ws_usuarios_modulo[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'WsUsuariosModulo';
  }


}
