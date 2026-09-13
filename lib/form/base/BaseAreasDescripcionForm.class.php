<?php

/**
 * AreasDescripcion form base class.
 *
 * @method AreasDescripcion getObject() Returns the current form's model object
 *
 * @package    ##PROJECT_NAME##
 * @subpackage form
 * @author     ##AUTHOR_NAME##
 */
abstract class BaseAreasDescripcionForm extends BaseFormPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'AREADESCRIPCION_ID' => new sfWidgetFormInputHidden(),
      'DESCRIPCION'        => new sfWidgetFormInputText(),
    ));

    $this->setValidators(array(
      'AREADESCRIPCION_ID' => new sfValidatorChoice(array('choices' => array($this->getObject()->getAreadescripcionId()), 'empty_value' => $this->getObject()->getAreadescripcionId(), 'required' => false)),
      'DESCRIPCION'        => new sfValidatorString(array('max_length' => 250, 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('areas_descripcion[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'AreasDescripcion';
  }


}
