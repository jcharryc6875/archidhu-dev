<?php

/**
 * FactVariable form base class.
 *
 * @method FactVariable getObject() Returns the current form's model object
 *
 * @package    ##PROJECT_NAME##
 * @subpackage form
 * @author     ##AUTHOR_NAME##
 */
abstract class BaseFactVariableForm extends BaseFormPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'FACTVARIABLE_ID' => new sfWidgetFormInputHidden(),
      'FACTTIPODATO_ID' => new sfWidgetFormPropelChoice(array('model' => 'FacturaTipo', 'add_empty' => false)),
      'FACTURATIPO_ID'  => new sfWidgetFormInputText(),
      'DESCRIPCION'     => new sfWidgetFormInputText(),
      'NOMBRE'          => new sfWidgetFormInputText(),
      'ORDEN'           => new sfWidgetFormInputText(),
      'ES_REQUERIDO'    => new sfWidgetFormInputText(),
      'ES_ACTUAL'       => new sfWidgetFormInputText(),
      'APPLY_ALL'       => new sfWidgetFormInputText(),
    ));

    $this->setValidators(array(
      'FACTVARIABLE_ID' => new sfValidatorChoice(array('choices' => array($this->getObject()->getFactvariableId()), 'empty_value' => $this->getObject()->getFactvariableId(), 'required' => false)),
      'FACTTIPODATO_ID' => new sfValidatorPropelChoice(array('model' => 'FacturaTipo', 'column' => 'FACTURATIPO_ID')),
      'FACTURATIPO_ID'  => new sfValidatorInteger(array('min' => -2147483648, 'max' => 2147483647)),
      'DESCRIPCION'     => new sfValidatorString(array('max_length' => 250, 'required' => false)),
      'NOMBRE'          => new sfValidatorString(array('max_length' => 20)),
      'ORDEN'           => new sfValidatorInteger(array('min' => -2147483648, 'max' => 2147483647, 'required' => false)),
      'ES_REQUERIDO'    => new sfValidatorInteger(array('min' => -128, 'max' => 127, 'required' => false)),
      'ES_ACTUAL'       => new sfValidatorInteger(array('min' => -128, 'max' => 127, 'required' => false)),
      'APPLY_ALL'       => new sfValidatorInteger(array('min' => -128, 'max' => 127, 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('fact_variable[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'FactVariable';
  }


}
