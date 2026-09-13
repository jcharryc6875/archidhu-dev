<?php

/**
 * RolPrivilegio filter form base class.
 *
 * @package    simad
 * @subpackage filter
 * @author     Your name here
 */
abstract class BaseRolPrivilegioFormFilter extends BaseFormFilterPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'ROL_ID'           => new sfWidgetFormPropelChoice(array('model' => 'Rol', 'add_empty' => true)),
      'FORMA_ID'         => new sfWidgetFormPropelChoice(array('model' => 'Forma', 'add_empty' => true)),
    ));

    $this->setValidators(array(
      'ROL_ID'           => new sfValidatorPropelChoice(array('required' => false, 'model' => 'Rol', 'column' => 'ROL_ID')),
      'FORMA_ID'         => new sfValidatorPropelChoice(array('required' => false, 'model' => 'Forma', 'column' => 'FORMA_ID')),
    ));

    $this->widgetSchema->setNameFormat('rol_privilegio_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'RolPrivilegio';
  }

  public function getFields()
  {
    return array(
      'ROLPRIVILEGIO_ID' => 'Number',
      'ROL_ID'           => 'ForeignKey',
      'FORMA_ID'         => 'ForeignKey',
    );
  }
}
