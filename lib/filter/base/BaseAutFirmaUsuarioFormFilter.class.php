<?php

/**
 * AutFirmaUsuario filter form base class.
 *
 * @package    simad
 * @subpackage filter
 * @author     Your name here
 */
abstract class BaseAutFirmaUsuarioFormFilter extends BaseFormFilterPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'USUARIO_ID'           => new sfWidgetFormPropelChoice(array('model' => 'Usuario', 'add_empty' => true)),
      'ROLFIRMAUSUARIO_ID'   => new sfWidgetFormPropelChoice(array('model' => 'RolFirmaUsuario', 'add_empty' => true)),
      'AUTORIZACIONFIRMA_ID' => new sfWidgetFormPropelChoice(array('model' => 'AutorizacionFirma', 'add_empty' => true)),
    ));

    $this->setValidators(array(
      'USUARIO_ID'           => new sfValidatorPropelChoice(array('required' => false, 'model' => 'Usuario', 'column' => 'USUARIO_ID')),
      'ROLFIRMAUSUARIO_ID'   => new sfValidatorPropelChoice(array('required' => false, 'model' => 'RolFirmaUsuario', 'column' => 'ROLFIRMAUSUARIO_ID')),
      'AUTORIZACIONFIRMA_ID' => new sfValidatorPropelChoice(array('required' => false, 'model' => 'AutorizacionFirma', 'column' => 'AUTORIZACIONFIRMA_ID')),
    ));

    $this->widgetSchema->setNameFormat('aut_firma_usuario_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'AutFirmaUsuario';
  }

  public function getFields()
  {
    return array(
      'AUTFIRMAUSUARIO_ID'   => 'Number',
      'USUARIO_ID'           => 'ForeignKey',
      'ROLFIRMAUSUARIO_ID'   => 'ForeignKey',
      'AUTORIZACIONFIRMA_ID' => 'ForeignKey',
    );
  }
}
