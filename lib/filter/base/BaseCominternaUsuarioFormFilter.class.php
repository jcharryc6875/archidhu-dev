<?php

/**
 * CominternaUsuario filter form base class.
 *
 * @package    simad
 * @subpackage filter
 * @author     Your name here
 */
abstract class BaseCominternaUsuarioFormFilter extends BaseFormFilterPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'ESTADOCOMINTERNA_ID'     => new sfWidgetFormPropelChoice(array('model' => 'EstadoComInterna', 'add_empty' => true)),
      'COMINTERNA_ID'           => new sfWidgetFormPropelChoice(array('model' => 'ComInterna', 'add_empty' => true)),
      'ROLUSUARIOCOMINTERNA_ID' => new sfWidgetFormPropelChoice(array('model' => 'RolusuarioCominterna', 'add_empty' => true)),
      'CARGOUSUARIO_ID'         => new sfWidgetFormPropelChoice(array('model' => 'CargoUsuario', 'add_empty' => true)),
      'USUARIO_ID'              => new sfWidgetFormPropelChoice(array('model' => 'Usuario', 'add_empty' => true)),
      'WF_EJECUTADO'            => new sfWidgetFormFilterInput(),
    ));

    $this->setValidators(array(
      'ESTADOCOMINTERNA_ID'     => new sfValidatorPropelChoice(array('required' => false, 'model' => 'EstadoComInterna', 'column' => 'ESTADOCOMINTERNA_ID')),
      'COMINTERNA_ID'           => new sfValidatorPropelChoice(array('required' => false, 'model' => 'ComInterna', 'column' => 'COMINTERNA_ID')),
      'ROLUSUARIOCOMINTERNA_ID' => new sfValidatorPropelChoice(array('required' => false, 'model' => 'RolusuarioCominterna', 'column' => 'ROLUSUARIOCOMINTERNA_ID')),
      'CARGOUSUARIO_ID'         => new sfValidatorPropelChoice(array('required' => false, 'model' => 'CargoUsuario', 'column' => 'CARGOUSUARIO_ID')),
      'USUARIO_ID'              => new sfValidatorPropelChoice(array('required' => false, 'model' => 'Usuario', 'column' => 'USUARIO_ID')),
      'WF_EJECUTADO'            => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
    ));

    $this->widgetSchema->setNameFormat('cominterna_usuario_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'CominternaUsuario';
  }

  public function getFields()
  {
    return array(
      'COMINTERNAUSUARIO_ID'    => 'Number',
      'ESTADOCOMINTERNA_ID'     => 'ForeignKey',
      'COMINTERNA_ID'           => 'ForeignKey',
      'ROLUSUARIOCOMINTERNA_ID' => 'ForeignKey',
      'CARGOUSUARIO_ID'         => 'ForeignKey',
      'USUARIO_ID'              => 'ForeignKey',
      'WF_EJECUTADO'            => 'Number',
    );
  }
}
