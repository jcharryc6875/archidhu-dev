<?php

/**
 * Consecutivo form base class.
 *
 * @method Consecutivo getObject() Returns the current form's model object
 *
 * @package    ##PROJECT_NAME##
 * @subpackage form
 * @author     ##AUTHOR_NAME##
 */
abstract class BaseConsecutivoForm extends BaseFormPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'CONSECUTIVO_ID' => new sfWidgetFormInputHidden(),
    ));

    $this->setValidators(array(
      'CONSECUTIVO_ID' => new sfValidatorChoice(array('choices' => array($this->getObject()->getConsecutivoId()), 'empty_value' => $this->getObject()->getConsecutivoId(), 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('consecutivo[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'Consecutivo';
  }


}
