<?php

/**
 * RolusuarioCominterna form base class.
 *
 * @method RolusuarioCominterna getObject() Returns the current form's model object
 *
 * @package    ##PROJECT_NAME##
 * @subpackage form
 * @author     ##AUTHOR_NAME##
 */
abstract class BaseRolusuarioCominternaForm extends BaseFormPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'ROLUSUARIOCOMINTERNA_ID' => new sfWidgetFormInputHidden(),
      'DESCRIPCION'             => new sfWidgetFormInputText(),
    ));

    $this->setValidators(array(
      'ROLUSUARIOCOMINTERNA_ID' => new sfValidatorChoice(array('choices' => array($this->getObject()->getRolusuariocominternaId()), 'empty_value' => $this->getObject()->getRolusuariocominternaId(), 'required' => false)),
      'DESCRIPCION'             => new sfValidatorString(array('max_length' => 250, 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('rolusuario_cominterna[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'RolusuarioCominterna';
  }


}
