<?php

/**
 * AutFirmaUsuario form base class.
 *
 * @method AutFirmaUsuario getObject() Returns the current form's model object
 *
 * @package    ##PROJECT_NAME##
 * @subpackage form
 * @author     ##AUTHOR_NAME##
 */
abstract class BaseAutFirmaUsuarioForm extends BaseFormPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'AUTFIRMAUSUARIO_ID'   => new sfWidgetFormInputHidden(),
      'USUARIO_ID'           => new sfWidgetFormPropelChoice(array('model' => 'Usuario', 'add_empty' => false)),
      'ROLFIRMAUSUARIO_ID'   => new sfWidgetFormPropelChoice(array('model' => 'RolFirmaUsuario', 'add_empty' => false)),
      'AUTORIZACIONFIRMA_ID' => new sfWidgetFormPropelChoice(array('model' => 'AutorizacionFirma', 'add_empty' => true)),
    ));

    $this->setValidators(array(
      'AUTFIRMAUSUARIO_ID'   => new sfValidatorChoice(array('choices' => array($this->getObject()->getAutfirmausuarioId()), 'empty_value' => $this->getObject()->getAutfirmausuarioId(), 'required' => false)),
      'USUARIO_ID'           => new sfValidatorPropelChoice(array('model' => 'Usuario', 'column' => 'USUARIO_ID')),
      'ROLFIRMAUSUARIO_ID'   => new sfValidatorPropelChoice(array('model' => 'RolFirmaUsuario', 'column' => 'ROLFIRMAUSUARIO_ID')),
      'AUTORIZACIONFIRMA_ID' => new sfValidatorPropelChoice(array('model' => 'AutorizacionFirma', 'column' => 'AUTORIZACIONFIRMA_ID', 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('aut_firma_usuario[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'AutFirmaUsuario';
  }


}
