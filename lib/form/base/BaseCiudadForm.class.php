<?php

/**
 * Ciudad form base class.
 *
 * @method Ciudad getObject() Returns the current form's model object
 *
 * @package    ##PROJECT_NAME##
 * @subpackage form
 * @author     ##AUTHOR_NAME##
 */
abstract class BaseCiudadForm extends BaseFormPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'CIUDAD_ID'       => new sfWidgetFormInputHidden(),
      'DEPARTAMENTO_ID' => new sfWidgetFormPropelChoice(array('model' => 'Departamento', 'add_empty' => false)),
      'NOMBRE'          => new sfWidgetFormInputText(),
    ));

    $this->setValidators(array(
      'CIUDAD_ID'       => new sfValidatorChoice(array('choices' => array($this->getObject()->getCiudadId()), 'empty_value' => $this->getObject()->getCiudadId(), 'required' => false)),
      'DEPARTAMENTO_ID' => new sfValidatorPropelChoice(array('model' => 'Departamento', 'column' => 'DEPARTAMENTO_ID')),
      'NOMBRE'          => new sfValidatorString(array('max_length' => 100, 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('ciudad[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'Ciudad';
  }


}
