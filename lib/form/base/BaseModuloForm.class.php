<?php

/**
 * Modulo form base class.
 *
 * @method Modulo getObject() Returns the current form's model object
 *
 * @package    ##PROJECT_NAME##
 * @subpackage form
 * @author     ##AUTHOR_NAME##
 */
abstract class BaseModuloForm extends BaseFormPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'MODULO_ID'   => new sfWidgetFormInputHidden(),
      'DESCRIPCION' => new sfWidgetFormInputText(),
    ));

    $this->setValidators(array(
      'MODULO_ID'   => new sfValidatorChoice(array('choices' => array($this->getObject()->getModuloId()), 'empty_value' => $this->getObject()->getModuloId(), 'required' => false)),
      'DESCRIPCION' => new sfValidatorString(array('max_length' => 250, 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('modulo[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'Modulo';
  }


}
