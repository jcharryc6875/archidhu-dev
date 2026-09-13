<?php

/**
 * Forma form base class.
 *
 * @method Forma getObject() Returns the current form's model object
 *
 * @package    ##PROJECT_NAME##
 * @subpackage form
 * @author     ##AUTHOR_NAME##
 */
abstract class BaseFormaForm extends BaseFormPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'FORMA_ID'    => new sfWidgetFormInputHidden(),
      'MODULO_ID'   => new sfWidgetFormPropelChoice(array('model' => 'Modulo', 'add_empty' => false)),
      'NOMBRE'      => new sfWidgetFormInputText(),
      'DESCRIPCION' => new sfWidgetFormInputText(),
      'RUTA'        => new sfWidgetFormInputText(),
      'IS_PUBLIC'   => new sfWidgetFormInputText(),
    ));

    $this->setValidators(array(
      'FORMA_ID'    => new sfValidatorChoice(array('choices' => array($this->getObject()->getFormaId()), 'empty_value' => $this->getObject()->getFormaId(), 'required' => false)),
      'MODULO_ID'   => new sfValidatorPropelChoice(array('model' => 'Modulo', 'column' => 'MODULO_ID')),
      'NOMBRE'      => new sfValidatorString(array('max_length' => 100, 'required' => false)),
      'DESCRIPCION' => new sfValidatorString(array('max_length' => 250, 'required' => false)),
      'RUTA'        => new sfValidatorString(array('max_length' => 1000, 'required' => false)),
      'IS_PUBLIC'   => new sfValidatorInteger(array('min' => -128, 'max' => 127, 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('forma[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'Forma';
  }


}
