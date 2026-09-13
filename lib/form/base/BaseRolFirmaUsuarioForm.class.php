<?php

/**
 * RolFirmaUsuario form base class.
 *
 * @method RolFirmaUsuario getObject() Returns the current form's model object
 *
 * @package    ##PROJECT_NAME##
 * @subpackage form
 * @author     ##AUTHOR_NAME##
 */
abstract class BaseRolFirmaUsuarioForm extends BaseFormPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'ROLFIRMAUSUARIO_ID' => new sfWidgetFormInputHidden(),
      'DESCRIPCION'        => new sfWidgetFormInputText(),
    ));

    $this->setValidators(array(
      'ROLFIRMAUSUARIO_ID' => new sfValidatorChoice(array('choices' => array($this->getObject()->getRolfirmausuarioId()), 'empty_value' => $this->getObject()->getRolfirmausuarioId(), 'required' => false)),
      'DESCRIPCION'        => new sfValidatorString(array('max_length' => 50, 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('rol_firma_usuario[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'RolFirmaUsuario';
  }


}
