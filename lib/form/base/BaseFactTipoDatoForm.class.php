<?php

/**
 * FactTipoDato form base class.
 *
 * @method FactTipoDato getObject() Returns the current form's model object
 *
 * @package    ##PROJECT_NAME##
 * @subpackage form
 * @author     ##AUTHOR_NAME##
 */
abstract class BaseFactTipoDatoForm extends BaseFormPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'FACTTIPODATO_ID' => new sfWidgetFormInputHidden(),
      'DESCRIPCION'     => new sfWidgetFormInputText(),
    ));

    $this->setValidators(array(
      'FACTTIPODATO_ID' => new sfValidatorChoice(array('choices' => array($this->getObject()->getFacttipodatoId()), 'empty_value' => $this->getObject()->getFacttipodatoId(), 'required' => false)),
      'DESCRIPCION'     => new sfValidatorString(array('max_length' => 50, 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('fact_tipo_dato[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'FactTipoDato';
  }


}
