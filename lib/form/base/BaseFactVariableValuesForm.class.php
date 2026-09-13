<?php

/**
 * FactVariableValues form base class.
 *
 * @method FactVariableValues getObject() Returns the current form's model object
 *
 * @package    ##PROJECT_NAME##
 * @subpackage form
 * @author     ##AUTHOR_NAME##
 */
abstract class BaseFactVariableValuesForm extends BaseFormPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'FACTVARIABLEVALUES_ID' => new sfWidgetFormInputHidden(),
      'FACTURA_ID'            => new sfWidgetFormPropelChoice(array('model' => 'Factura', 'add_empty' => false)),
      'FACTVARIABLE_ID'       => new sfWidgetFormPropelChoice(array('model' => 'FactVariable', 'add_empty' => false)),
      'VARIABLE_VALUE'        => new sfWidgetFormInputText(),
    ));

    $this->setValidators(array(
      'FACTVARIABLEVALUES_ID' => new sfValidatorChoice(array('choices' => array($this->getObject()->getFactvariablevaluesId()), 'empty_value' => $this->getObject()->getFactvariablevaluesId(), 'required' => false)),
      'FACTURA_ID'            => new sfValidatorPropelChoice(array('model' => 'Factura', 'column' => 'FACTURA_ID')),
      'FACTVARIABLE_ID'       => new sfValidatorPropelChoice(array('model' => 'FactVariable', 'column' => 'FACTVARIABLE_ID')),
      'VARIABLE_VALUE'        => new sfValidatorString(array('max_length' => 4000, 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('fact_variable_values[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'FactVariableValues';
  }


}
