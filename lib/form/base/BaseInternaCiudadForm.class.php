<?php

/**
 * InternaCiudad form base class.
 *
 * @method InternaCiudad getObject() Returns the current form's model object
 *
 * @package    ##PROJECT_NAME##
 * @subpackage form
 * @author     ##AUTHOR_NAME##
 */
abstract class BaseInternaCiudadForm extends BaseFormPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'INTERNACIUDAD_ID'     => new sfWidgetFormInputHidden(),
      'CIUDAD_ID'            => new sfWidgetFormPropelChoice(array('model' => 'Ciudad', 'add_empty' => false)),
      'COMINTERNA_ID'        => new sfWidgetFormPropelChoice(array('model' => 'ComInterna', 'add_empty' => false)),
      'ROLCIUDADINTERNA__ID' => new sfWidgetFormPropelChoice(array('model' => 'RolCiudadInerna', 'add_empty' => false)),
    ));

    $this->setValidators(array(
      'INTERNACIUDAD_ID'     => new sfValidatorChoice(array('choices' => array($this->getObject()->getInternaciudadId()), 'empty_value' => $this->getObject()->getInternaciudadId(), 'required' => false)),
      'CIUDAD_ID'            => new sfValidatorPropelChoice(array('model' => 'Ciudad', 'column' => 'CIUDAD_ID')),
      'COMINTERNA_ID'        => new sfValidatorPropelChoice(array('model' => 'ComInterna', 'column' => 'COMINTERNA_ID')),
      'ROLCIUDADINTERNA__ID' => new sfValidatorPropelChoice(array('model' => 'RolCiudadInerna', 'column' => 'ROLCIUDADINTERNA__ID')),
    ));

    $this->widgetSchema->setNameFormat('interna_ciudad[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'InternaCiudad';
  }


}
