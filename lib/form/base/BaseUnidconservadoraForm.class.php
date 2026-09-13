<?php

/**
 * Unidconservadora form base class.
 *
 * @method Unidconservadora getObject() Returns the current form's model object
 *
 * @package    ##PROJECT_NAME##
 * @subpackage form
 * @author     ##AUTHOR_NAME##
 */
abstract class BaseUnidconservadoraForm extends BaseFormPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'UNIDCONSERVADORA_ID' => new sfWidgetFormInputHidden(),
      'DESCRIPCION'         => new sfWidgetFormInputText(),
    ));

    $this->setValidators(array(
      'UNIDCONSERVADORA_ID' => new sfValidatorChoice(array('choices' => array($this->getObject()->getUnidconservadoraId()), 'empty_value' => $this->getObject()->getUnidconservadoraId(), 'required' => false)),
      'DESCRIPCION'         => new sfValidatorString(array('max_length' => 250, 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('unidconservadora[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'Unidconservadora';
  }


}
