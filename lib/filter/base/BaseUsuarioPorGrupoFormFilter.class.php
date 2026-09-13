<?php

/**
 * UsuarioPorGrupo filter form base class.
 *
 * @package    simad
 * @subpackage filter
 * @author     Your name here
 */
abstract class BaseUsuarioPorGrupoFormFilter extends BaseFormFilterPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'USUARIO_ID'         => new sfWidgetFormPropelChoice(array('model' => 'Usuario', 'add_empty' => true)),
      'GRUPOUSUARI_ID'     => new sfWidgetFormPropelChoice(array('model' => 'GrupoUsuario', 'add_empty' => true)),
    ));

    $this->setValidators(array(
      'USUARIO_ID'         => new sfValidatorPropelChoice(array('required' => false, 'model' => 'Usuario', 'column' => 'USUARIO_ID')),
      'GRUPOUSUARI_ID'     => new sfValidatorPropelChoice(array('required' => false, 'model' => 'GrupoUsuario', 'column' => 'GRUPOUSUARI_ID')),
    ));

    $this->widgetSchema->setNameFormat('usuario_por_grupo_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'UsuarioPorGrupo';
  }

  public function getFields()
  {
    return array(
      'USUARIOPORGRUPO_ID' => 'Number',
      'USUARIO_ID'         => 'ForeignKey',
      'GRUPOUSUARI_ID'     => 'ForeignKey',
    );
  }
}
