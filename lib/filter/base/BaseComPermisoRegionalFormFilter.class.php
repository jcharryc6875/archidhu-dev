<?php

/**
 * ComPermisoRegional filter form base class.
 *
 * @package    simad
 * @subpackage filter
 * @author     Your name here
 */
abstract class BaseComPermisoRegionalFormFilter extends BaseFormFilterPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'REGIONAL_ID'           => new sfWidgetFormPropelChoice(array('model' => 'Regional', 'add_empty' => true)),
      'USUARIO_ID'            => new sfWidgetFormPropelChoice(array('model' => 'Usuario', 'add_empty' => true)),
    ));

    $this->setValidators(array(
      'REGIONAL_ID'           => new sfValidatorPropelChoice(array('required' => false, 'model' => 'Regional', 'column' => 'REGIONAL_ID')),
      'USUARIO_ID'            => new sfValidatorPropelChoice(array('required' => false, 'model' => 'Usuario', 'column' => 'USUARIO_ID')),
    ));

    $this->widgetSchema->setNameFormat('com_permiso_regional_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'ComPermisoRegional';
  }

  public function getFields()
  {
    return array(
      'COMPERMISOREGIONAL_ID' => 'Number',
      'REGIONAL_ID'           => 'ForeignKey',
      'USUARIO_ID'            => 'ForeignKey',
    );
  }
}
