<?php

/**
 * RolPorUsuario form base class.
 *
 * @method RolPorUsuario getObject() Returns the current form's model object
 *
 * @package    ##PROJECT_NAME##
 * @subpackage form
 * @author     ##AUTHOR_NAME##
 */
abstract class BaseRolPorUsuarioForm extends BaseFormPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'ROLPORUSUARIO_ID' => new sfWidgetFormInputHidden(),
      'USUARIO_ID'       => new sfWidgetFormPropelChoice(array('model' => 'Usuario', 'add_empty' => false)),
      'ROL_ID'           => new sfWidgetFormPropelChoice(array('model' => 'Rol', 'add_empty' => false)),
    ));

    $this->setValidators(array(
      'ROLPORUSUARIO_ID' => new sfValidatorChoice(array('choices' => array($this->getObject()->getRolporusuarioId()), 'empty_value' => $this->getObject()->getRolporusuarioId(), 'required' => false)),
      'USUARIO_ID'       => new sfValidatorPropelChoice(array('model' => 'Usuario', 'column' => 'USUARIO_ID')),
      'ROL_ID'           => new sfValidatorPropelChoice(array('model' => 'Rol', 'column' => 'ROL_ID')),
    ));

    $this->widgetSchema->setNameFormat('rol_por_usuario[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'RolPorUsuario';
  }


}
