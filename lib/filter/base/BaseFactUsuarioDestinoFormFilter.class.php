<?php

/**
 * FactUsuarioDestino filter form base class.
 *
 * @package    simad
 * @subpackage filter
 * @author     Your name here
 */
abstract class BaseFactUsuarioDestinoFormFilter extends BaseFormFilterPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'USUARIO_ID'                   => new sfWidgetFormPropelChoice(array('model' => 'Usuario', 'add_empty' => true)),
      'FACTURAVITACORA_ID'           => new sfWidgetFormPropelChoice(array('model' => 'FacturaVitacora', 'add_empty' => true)),
      'FACTURAVITACORAROLUSUARIO_ID' => new sfWidgetFormPropelChoice(array('model' => 'FacturaVitacoraRolUsuario', 'add_empty' => true)),
    ));

    $this->setValidators(array(
      'USUARIO_ID'                   => new sfValidatorPropelChoice(array('required' => false, 'model' => 'Usuario', 'column' => 'USUARIO_ID')),
      'FACTURAVITACORA_ID'           => new sfValidatorPropelChoice(array('required' => false, 'model' => 'FacturaVitacora', 'column' => 'FACTURAVITACORA_ID')),
      'FACTURAVITACORAROLUSUARIO_ID' => new sfValidatorPropelChoice(array('required' => false, 'model' => 'FacturaVitacoraRolUsuario', 'column' => 'FACTURAVITACORAROLUSUARIO_ID')),
    ));

    $this->widgetSchema->setNameFormat('fact_usuario_destino_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'FactUsuarioDestino';
  }

  public function getFields()
  {
    return array(
      'FACTUSUARIODESTINO_ID'        => 'Number',
      'USUARIO_ID'                   => 'ForeignKey',
      'FACTURAVITACORA_ID'           => 'ForeignKey',
      'FACTURAVITACORAROLUSUARIO_ID' => 'ForeignKey',
    );
  }
}
