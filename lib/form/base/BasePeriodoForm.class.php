<?php

/**
 * Periodo form base class.
 *
 * @method Periodo getObject() Returns the current form's model object
 *
 * @package    ##PROJECT_NAME##
 * @subpackage form
 * @author     ##AUTHOR_NAME##
 */
abstract class BasePeriodoForm extends BaseFormPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'PERIODO_ID'        => new sfWidgetFormInputHidden(),
      'DESCRIPCION'       => new sfWidgetFormInputText(),
      'ES_PERIODO_ACTUAL' => new sfWidgetFormInputText(),
    ));

    $this->setValidators(array(
      'PERIODO_ID'        => new sfValidatorChoice(array('choices' => array($this->getObject()->getPeriodoId()), 'empty_value' => $this->getObject()->getPeriodoId(), 'required' => false)),
      'DESCRIPCION'       => new sfValidatorString(array('max_length' => 250, 'required' => false)),
      'ES_PERIODO_ACTUAL' => new sfValidatorInteger(array('min' => -32768, 'max' => 32767, 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('periodo[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'Periodo';
  }


}
