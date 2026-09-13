<?php

/**
 * UsuarioPrivilegio form base class.
 *
 * @method UsuarioPrivilegio getObject() Returns the current form's model object
 *
 * @package    ##PROJECT_NAME##
 * @subpackage form
 * @author     ##AUTHOR_NAME##
 */
abstract class BaseUsuarioPrivilegioForm extends BaseFormPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'USUARIOPRIVILEGIO_ID' => new sfWidgetFormInputHidden(),
      'FORMA_ID'             => new sfWidgetFormPropelChoice(array('model' => 'Forma', 'add_empty' => false)),
      'USUARIO_ID'           => new sfWidgetFormPropelChoice(array('model' => 'Usuario', 'add_empty' => false)),
    ));

    $this->setValidators(array(
      'USUARIOPRIVILEGIO_ID' => new sfValidatorChoice(array('choices' => array($this->getObject()->getUsuarioprivilegioId()), 'empty_value' => $this->getObject()->getUsuarioprivilegioId(), 'required' => false)),
      'FORMA_ID'             => new sfValidatorPropelChoice(array('model' => 'Forma', 'column' => 'FORMA_ID')),
      'USUARIO_ID'           => new sfValidatorPropelChoice(array('model' => 'Usuario', 'column' => 'USUARIO_ID')),
    ));

    $this->widgetSchema->setNameFormat('usuario_privilegio[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'UsuarioPrivilegio';
  }


}
