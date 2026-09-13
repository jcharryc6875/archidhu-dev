<?php

/**
 * WsUsuarios filter form base class.
 *
 * @package    simad
 * @subpackage filter
 * @author     Your name here
 */
abstract class BaseWsUsuariosFormFilter extends BaseFormFilterPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'USUARIO'       => new sfWidgetFormFilterInput(),
      'PASSWORD'      => new sfWidgetFormFilterInput(),
      'NOMBRE'        => new sfWidgetFormFilterInput(),
      'DIRECCION'     => new sfWidgetFormFilterInput(),
      'TELEFONO'      => new sfWidgetFormFilterInput(),
      'EMAIL'         => new sfWidgetFormFilterInput(),
    ));

    $this->setValidators(array(
      'USUARIO'       => new sfValidatorPass(array('required' => false)),
      'PASSWORD'      => new sfValidatorPass(array('required' => false)),
      'NOMBRE'        => new sfValidatorPass(array('required' => false)),
      'DIRECCION'     => new sfValidatorPass(array('required' => false)),
      'TELEFONO'      => new sfValidatorPass(array('required' => false)),
      'EMAIL'         => new sfValidatorPass(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('ws_usuarios_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'WsUsuarios';
  }

  public function getFields()
  {
    return array(
      'WSUSUARIOS_ID' => 'Number',
      'USUARIO'       => 'Text',
      'PASSWORD'      => 'Text',
      'NOMBRE'        => 'Text',
      'DIRECCION'     => 'Text',
      'TELEFONO'      => 'Text',
      'EMAIL'         => 'Text',
    );
  }
}
