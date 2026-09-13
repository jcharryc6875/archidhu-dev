<?php

/**
 * RolFirmaUsuario filter form base class.
 *
 * @package    simad
 * @subpackage filter
 * @author     Your name here
 */
abstract class BaseRolFirmaUsuarioFormFilter extends BaseFormFilterPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'DESCRIPCION'        => new sfWidgetFormFilterInput(),
    ));

    $this->setValidators(array(
      'DESCRIPCION'        => new sfValidatorPass(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('rol_firma_usuario_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'RolFirmaUsuario';
  }

  public function getFields()
  {
    return array(
      'ROLFIRMAUSUARIO_ID' => 'Number',
      'DESCRIPCION'        => 'Text',
    );
  }
}
