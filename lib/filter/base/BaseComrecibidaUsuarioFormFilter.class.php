<?php

/**
 * ComrecibidaUsuario filter form base class.
 *
 * @package    simad
 * @subpackage filter
 * @author     Your name here
 */
abstract class BaseComrecibidaUsuarioFormFilter extends BaseFormFilterPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'ROLUSUARIORECIBIDAID'  => new sfWidgetFormPropelChoice(array('model' => 'RolUsuarioRecibida', 'add_empty' => true)),
      'USUARIO_ID'            => new sfWidgetFormPropelChoice(array('model' => 'Usuario', 'add_empty' => true)),
      'ESTADOCOMRECIBIDA_ID'  => new sfWidgetFormPropelChoice(array('model' => 'EstadoComRecibida', 'add_empty' => true)),
      'COMRECIBIDA_ID'        => new sfWidgetFormPropelChoice(array('model' => 'ComRecibida', 'add_empty' => true)),
      'ESTA_ASIGNADA'         => new sfWidgetFormFilterInput(),
      'CARGOUSUARIO_ID'       => new sfWidgetFormPropelChoice(array('model' => 'CargoUsuario', 'add_empty' => true)),
      'WF_EJECUTADO'          => new sfWidgetFormFilterInput(),
    ));

    $this->setValidators(array(
      'ROLUSUARIORECIBIDAID'  => new sfValidatorPropelChoice(array('required' => false, 'model' => 'RolUsuarioRecibida', 'column' => 'ROLUSUARIORECIBIDAID')),
      'USUARIO_ID'            => new sfValidatorPropelChoice(array('required' => false, 'model' => 'Usuario', 'column' => 'USUARIO_ID')),
      'ESTADOCOMRECIBIDA_ID'  => new sfValidatorPropelChoice(array('required' => false, 'model' => 'EstadoComRecibida', 'column' => 'ESTADOCOMRECIBIDA_ID')),
      'COMRECIBIDA_ID'        => new sfValidatorPropelChoice(array('required' => false, 'model' => 'ComRecibida', 'column' => 'COMRECIBIDA_ID')),
      'ESTA_ASIGNADA'         => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'CARGOUSUARIO_ID'       => new sfValidatorPropelChoice(array('required' => false, 'model' => 'CargoUsuario', 'column' => 'CARGOUSUARIO_ID')),
      'WF_EJECUTADO'          => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
    ));

    $this->widgetSchema->setNameFormat('comrecibida_usuario_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'ComrecibidaUsuario';
  }

  public function getFields()
  {
    return array(
      'COMRECIBIDAUSUARIO_ID' => 'Number',
      'ROLUSUARIORECIBIDAID'  => 'ForeignKey',
      'USUARIO_ID'            => 'ForeignKey',
      'ESTADOCOMRECIBIDA_ID'  => 'ForeignKey',
      'COMRECIBIDA_ID'        => 'ForeignKey',
      'ESTA_ASIGNADA'         => 'Number',
      'CARGOUSUARIO_ID'       => 'ForeignKey',
      'WF_EJECUTADO'          => 'Number',
    );
  }
}
