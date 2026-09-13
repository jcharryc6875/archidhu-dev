<?php

/**
 * Rol filter form base class.
 *
 * @package    simad
 * @subpackage filter
 * @author     Your name here
 */
abstract class BaseRolFormFilter extends BaseFormFilterPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'DESCRIPCION' => new sfWidgetFormFilterInput(),
      'ENTIDAD_ID'  => new sfWidgetFormPropelChoice(array('model' => 'Entidad', 'add_empty' => true)),
    ));

    $this->setValidators(array(
      'DESCRIPCION' => new sfValidatorPass(array('required' => false)),
      'ENTIDAD_ID'  => new sfValidatorPropelChoice(array('required' => false, 'model' => 'Entidad', 'column' => 'ENTIDAD_ID')),
    ));

    $this->widgetSchema->setNameFormat('rol_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'Rol';
  }

  public function getFields()
  {
    return array(
      'ROL_ID'      => 'Number',
      'DESCRIPCION' => 'Text',
      'ENTIDAD_ID'  => 'ForeignKey',
    );
  }
}
