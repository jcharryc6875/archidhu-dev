<?php

/**
 * Pais form base class.
 *
 * @method Pais getObject() Returns the current form's model object
 *
 * @package    ##PROJECT_NAME##
 * @subpackage form
 * @author     ##AUTHOR_NAME##
 */
abstract class BasePaisForm extends BaseFormPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'PAIS_ID' => new sfWidgetFormInputHidden(),
      'NOMBRE'  => new sfWidgetFormInputText(),
    ));

    $this->setValidators(array(
      'PAIS_ID' => new sfValidatorChoice(array('choices' => array($this->getObject()->getPaisId()), 'empty_value' => $this->getObject()->getPaisId(), 'required' => false)),
      'NOMBRE'  => new sfValidatorString(array('max_length' => 100, 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('pais[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'Pais';
  }


}
