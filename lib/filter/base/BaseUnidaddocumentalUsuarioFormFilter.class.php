<?php

/**
 * UnidaddocumentalUsuario filter form base class.
 *
 * @package    simad
 * @subpackage filter
 * @author     Your name here
 */
abstract class BaseUnidaddocumentalUsuarioFormFilter extends BaseFormFilterPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'ROLUSUUNIDADDOC_ID'         => new sfWidgetFormPropelChoice(array('model' => 'RolUsuUnidadDoc', 'add_empty' => true)),
      'USUARIO_ID'                 => new sfWidgetFormPropelChoice(array('model' => 'Usuario', 'add_empty' => true)),
      'UNIDADDOCUMENTAL_ID'        => new sfWidgetFormPropelChoice(array('model' => 'UnidadDocumental', 'add_empty' => true)),
    ));

    $this->setValidators(array(
      'ROLUSUUNIDADDOC_ID'         => new sfValidatorPropelChoice(array('required' => false, 'model' => 'RolUsuUnidadDoc', 'column' => 'ROLUSUUNIDADDOC_ID')),
      'USUARIO_ID'                 => new sfValidatorPropelChoice(array('required' => false, 'model' => 'Usuario', 'column' => 'USUARIO_ID')),
      'UNIDADDOCUMENTAL_ID'        => new sfValidatorPropelChoice(array('required' => false, 'model' => 'UnidadDocumental', 'column' => 'UNIDADDOCUMENTAL_ID')),
    ));

    $this->widgetSchema->setNameFormat('unidaddocumental_usuario_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'UnidaddocumentalUsuario';
  }

  public function getFields()
  {
    return array(
      'UNIDADDOCUMENTALUSUARIO_ID' => 'Number',
      'ROLUSUUNIDADDOC_ID'         => 'ForeignKey',
      'USUARIO_ID'                 => 'ForeignKey',
      'UNIDADDOCUMENTAL_ID'        => 'ForeignKey',
    );
  }
}
