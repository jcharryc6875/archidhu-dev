<?php

/**
 * PermisoUsuarioUniddoc filter form base class.
 *
 * @package    simad
 * @subpackage filter
 * @author     Your name here
 */
abstract class BasePermisoUsuarioUniddocFormFilter extends BaseFormFilterPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'UNIDADDOCUMENTAL_ID'      => new sfWidgetFormPropelChoice(array('model' => 'UnidadDocumental', 'add_empty' => true)),
      'USUARIO_ID'               => new sfWidgetFormPropelChoice(array('model' => 'Usuario', 'add_empty' => true)),
    ));

    $this->setValidators(array(
      'UNIDADDOCUMENTAL_ID'      => new sfValidatorPropelChoice(array('required' => false, 'model' => 'UnidadDocumental', 'column' => 'UNIDADDOCUMENTAL_ID')),
      'USUARIO_ID'               => new sfValidatorPropelChoice(array('required' => false, 'model' => 'Usuario', 'column' => 'USUARIO_ID')),
    ));

    $this->widgetSchema->setNameFormat('permiso_usuario_uniddoc_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'PermisoUsuarioUniddoc';
  }

  public function getFields()
  {
    return array(
      'PERMISOUSUARIOUNIDDOC_ID' => 'Number',
      'UNIDADDOCUMENTAL_ID'      => 'ForeignKey',
      'USUARIO_ID'               => 'ForeignKey',
    );
  }
}
