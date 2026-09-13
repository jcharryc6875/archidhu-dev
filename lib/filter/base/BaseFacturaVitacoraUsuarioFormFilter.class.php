<?php

/**
 * FacturaVitacoraUsuario filter form base class.
 *
 * @package    simad
 * @subpackage filter
 * @author     Your name here
 */
abstract class BaseFacturaVitacoraUsuarioFormFilter extends BaseFormFilterPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'FACTURAVITACORAROLUSUARIO_ID' => new sfWidgetFormPropelChoice(array('model' => 'FacturaVitacoraRolUsuario', 'add_empty' => true)),
      'FACTURAVITACORA_ID'           => new sfWidgetFormPropelChoice(array('model' => 'FacturaVitacora', 'add_empty' => true)),
      'USUARIO_ID'                   => new sfWidgetFormPropelChoice(array('model' => 'Usuario', 'add_empty' => true)),
    ));

    $this->setValidators(array(
      'FACTURAVITACORAROLUSUARIO_ID' => new sfValidatorPropelChoice(array('required' => false, 'model' => 'FacturaVitacoraRolUsuario', 'column' => 'FACTURAVITACORAROLUSUARIO_ID')),
      'FACTURAVITACORA_ID'           => new sfValidatorPropelChoice(array('required' => false, 'model' => 'FacturaVitacora', 'column' => 'FACTURAVITACORA_ID')),
      'USUARIO_ID'                   => new sfValidatorPropelChoice(array('required' => false, 'model' => 'Usuario', 'column' => 'USUARIO_ID')),
    ));

    $this->widgetSchema->setNameFormat('factura_vitacora_usuario_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'FacturaVitacoraUsuario';
  }

  public function getFields()
  {
    return array(
      'FACTURAVITACORAUSUARIO_ID'    => 'Number',
      'FACTURAVITACORAROLUSUARIO_ID' => 'ForeignKey',
      'FACTURAVITACORA_ID'           => 'ForeignKey',
      'USUARIO_ID'                   => 'ForeignKey',
    );
  }
}
