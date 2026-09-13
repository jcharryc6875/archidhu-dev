<?php

/**
 * ProvItemFlujo form base class.
 *
 * @method ProvItemFlujo getObject() Returns the current form's model object
 *
 * @package    ##PROJECT_NAME##
 * @subpackage form
 * @author     ##AUTHOR_NAME##
 */
abstract class BaseProvItemFlujoForm extends BaseFormPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'PROV_ITEM_FLUJO_ID' => new sfWidgetFormInputHidden(),
      'DESCRIPCION'        => new sfWidgetFormInputText(),
    ));

    $this->setValidators(array(
      'PROV_ITEM_FLUJO_ID' => new sfValidatorChoice(array('choices' => array($this->getObject()->getProvItemFlujoId()), 'empty_value' => $this->getObject()->getProvItemFlujoId(), 'required' => false)),
      'DESCRIPCION'        => new sfValidatorString(array('max_length' => 250, 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('prov_item_flujo[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'ProvItemFlujo';
  }


}
