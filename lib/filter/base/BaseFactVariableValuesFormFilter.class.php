<?php

/**
 * FactVariableValues filter form base class.
 *
 * @package    simad
 * @subpackage filter
 * @author     Your name here
 */
abstract class BaseFactVariableValuesFormFilter extends BaseFormFilterPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'FACTURA_ID'            => new sfWidgetFormPropelChoice(array('model' => 'Factura', 'add_empty' => true)),
      'FACTVARIABLE_ID'       => new sfWidgetFormPropelChoice(array('model' => 'FactVariable', 'add_empty' => true)),
      'VARIABLE_VALUE'        => new sfWidgetFormFilterInput(),
    ));

    $this->setValidators(array(
      'FACTURA_ID'            => new sfValidatorPropelChoice(array('required' => false, 'model' => 'Factura', 'column' => 'FACTURA_ID')),
      'FACTVARIABLE_ID'       => new sfValidatorPropelChoice(array('required' => false, 'model' => 'FactVariable', 'column' => 'FACTVARIABLE_ID')),
      'VARIABLE_VALUE'        => new sfValidatorPass(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('fact_variable_values_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'FactVariableValues';
  }

  public function getFields()
  {
    return array(
      'FACTVARIABLEVALUES_ID' => 'Number',
      'FACTURA_ID'            => 'ForeignKey',
      'FACTVARIABLE_ID'       => 'ForeignKey',
      'VARIABLE_VALUE'        => 'Text',
    );
  }
}
