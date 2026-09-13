<?php

/**
 * Idioma form base class.
 *
 * @method Idioma getObject() Returns the current form's model object
 *
 * @package    ##PROJECT_NAME##
 * @subpackage form
 * @author     ##AUTHOR_NAME##
 */
abstract class BaseIdiomaForm extends BaseFormPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'IDIOMA_ID'   => new sfWidgetFormInputHidden(),
      'DESCRIPCION' => new sfWidgetFormInputText(),
      'CODIGO'      => new sfWidgetFormInputText(),
    ));

    $this->setValidators(array(
      'IDIOMA_ID'   => new sfValidatorChoice(array('choices' => array($this->getObject()->getIdiomaId()), 'empty_value' => $this->getObject()->getIdiomaId(), 'required' => false)),
      'DESCRIPCION' => new sfValidatorString(array('max_length' => 250, 'required' => false)),
      'CODIGO'      => new sfValidatorString(array('max_length' => 50, 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('idioma[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'Idioma';
  }


}
