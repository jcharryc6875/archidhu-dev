<?php

/**
 * WsUsuariosModulo filter form base class.
 *
 * @package    simad
 * @subpackage filter
 * @author     Your name here
 */
abstract class BaseWsUsuariosModuloFormFilter extends BaseFormFilterPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'MODULO_ID'           => new sfWidgetFormPropelChoice(array('model' => 'Modulo', 'add_empty' => true)),
      'WSUSUARIOS_ID'       => new sfWidgetFormPropelChoice(array('model' => 'WsUsuarios', 'add_empty' => true)),
    ));

    $this->setValidators(array(
      'MODULO_ID'           => new sfValidatorPropelChoice(array('required' => false, 'model' => 'Modulo', 'column' => 'MODULO_ID')),
      'WSUSUARIOS_ID'       => new sfValidatorPropelChoice(array('required' => false, 'model' => 'WsUsuarios', 'column' => 'WSUSUARIOS_ID')),
    ));

    $this->widgetSchema->setNameFormat('ws_usuarios_modulo_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'WsUsuariosModulo';
  }

  public function getFields()
  {
    return array(
      'WSUSUARIOSMODULO_ID' => 'Number',
      'MODULO_ID'           => 'ForeignKey',
      'WSUSUARIOS_ID'       => 'ForeignKey',
    );
  }
}
