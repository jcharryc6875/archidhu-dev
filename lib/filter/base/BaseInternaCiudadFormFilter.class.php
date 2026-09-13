<?php

/**
 * InternaCiudad filter form base class.
 *
 * @package    simad
 * @subpackage filter
 * @author     Your name here
 */
abstract class BaseInternaCiudadFormFilter extends BaseFormFilterPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'CIUDAD_ID'            => new sfWidgetFormPropelChoice(array('model' => 'Ciudad', 'add_empty' => true)),
      'COMINTERNA_ID'        => new sfWidgetFormPropelChoice(array('model' => 'ComInterna', 'add_empty' => true)),
      'ROLCIUDADINTERNA__ID' => new sfWidgetFormPropelChoice(array('model' => 'RolCiudadInerna', 'add_empty' => true)),
    ));

    $this->setValidators(array(
      'CIUDAD_ID'            => new sfValidatorPropelChoice(array('required' => false, 'model' => 'Ciudad', 'column' => 'CIUDAD_ID')),
      'COMINTERNA_ID'        => new sfValidatorPropelChoice(array('required' => false, 'model' => 'ComInterna', 'column' => 'COMINTERNA_ID')),
      'ROLCIUDADINTERNA__ID' => new sfValidatorPropelChoice(array('required' => false, 'model' => 'RolCiudadInerna', 'column' => 'ROLCIUDADINTERNA__ID')),
    ));

    $this->widgetSchema->setNameFormat('interna_ciudad_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'InternaCiudad';
  }

  public function getFields()
  {
    return array(
      'INTERNACIUDAD_ID'     => 'Number',
      'CIUDAD_ID'            => 'ForeignKey',
      'COMINTERNA_ID'        => 'ForeignKey',
      'ROLCIUDADINTERNA__ID' => 'ForeignKey',
    );
  }
}
