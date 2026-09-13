<?php

/**
 * RolCiudadInerna filter form base class.
 *
 * @package    simad
 * @subpackage filter
 * @author     Your name here
 */
abstract class BaseRolCiudadInernaFormFilter extends BaseFormFilterPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'DESCRIPCION'          => new sfWidgetFormFilterInput(),
    ));

    $this->setValidators(array(
      'DESCRIPCION'          => new sfValidatorPass(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('rol_ciudad_inerna_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'RolCiudadInerna';
  }

  public function getFields()
  {
    return array(
      'ROLCIUDADINTERNA__ID' => 'Number',
      'DESCRIPCION'          => 'Text',
    );
  }
}
