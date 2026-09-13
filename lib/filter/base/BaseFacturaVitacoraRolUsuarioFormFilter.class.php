<?php

/**
 * FacturaVitacoraRolUsuario filter form base class.
 *
 * @package    simad
 * @subpackage filter
 * @author     Your name here
 */
abstract class BaseFacturaVitacoraRolUsuarioFormFilter extends BaseFormFilterPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'DESCRIPCION'                  => new sfWidgetFormFilterInput(),
    ));

    $this->setValidators(array(
      'DESCRIPCION'                  => new sfValidatorPass(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('factura_vitacora_rol_usuario_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'FacturaVitacoraRolUsuario';
  }

  public function getFields()
  {
    return array(
      'FACTURAVITACORAROLUSUARIO_ID' => 'Number',
      'DESCRIPCION'                  => 'Text',
    );
  }
}
