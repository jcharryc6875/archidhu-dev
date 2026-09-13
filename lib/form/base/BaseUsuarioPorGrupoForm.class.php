<?php

/**
 * UsuarioPorGrupo form base class.
 *
 * @method UsuarioPorGrupo getObject() Returns the current form's model object
 *
 * @package    ##PROJECT_NAME##
 * @subpackage form
 * @author     ##AUTHOR_NAME##
 */
abstract class BaseUsuarioPorGrupoForm extends BaseFormPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'USUARIOPORGRUPO_ID' => new sfWidgetFormInputHidden(),
      'USUARIO_ID'         => new sfWidgetFormPropelChoice(array('model' => 'Usuario', 'add_empty' => false)),
      'GRUPOUSUARI_ID'     => new sfWidgetFormPropelChoice(array('model' => 'GrupoUsuario', 'add_empty' => false)),
    ));

    $this->setValidators(array(
      'USUARIOPORGRUPO_ID' => new sfValidatorChoice(array('choices' => array($this->getObject()->getUsuarioporgrupoId()), 'empty_value' => $this->getObject()->getUsuarioporgrupoId(), 'required' => false)),
      'USUARIO_ID'         => new sfValidatorPropelChoice(array('model' => 'Usuario', 'column' => 'USUARIO_ID')),
      'GRUPOUSUARI_ID'     => new sfValidatorPropelChoice(array('model' => 'GrupoUsuario', 'column' => 'GRUPOUSUARI_ID')),
    ));

    $this->widgetSchema->setNameFormat('usuario_por_grupo[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'UsuarioPorGrupo';
  }


}
