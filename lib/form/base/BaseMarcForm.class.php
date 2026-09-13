<?php

/**
 * Marc form base class.
 *
 * @method Marc getObject() Returns the current form's model object
 *
 * @package    ##PROJECT_NAME##
 * @subpackage form
 * @author     ##AUTHOR_NAME##
 */
abstract class BaseMarcForm extends BaseFormPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'MARC_ID'     => new sfWidgetFormInputHidden(),
      'CODIGO'      => new sfWidgetFormInputText(),
      'DESCRIPCION' => new sfWidgetFormInputText(),
    ));

    $this->setValidators(array(
      'MARC_ID'     => new sfValidatorChoice(array('choices' => array($this->getObject()->getMarcId()), 'empty_value' => $this->getObject()->getMarcId(), 'required' => false)),
      'CODIGO'      => new sfValidatorString(array('max_length' => 50, 'required' => false)),
      'DESCRIPCION' => new sfValidatorString(array('max_length' => 250, 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('marc[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'Marc';
  }


}
