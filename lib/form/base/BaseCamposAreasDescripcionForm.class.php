<?php

/**
 * CamposAreasDescripcion form base class.
 *
 * @method CamposAreasDescripcion getObject() Returns the current form's model object
 *
 * @package    ##PROJECT_NAME##
 * @subpackage form
 * @author     ##AUTHOR_NAME##
 */
abstract class BaseCamposAreasDescripcionForm extends BaseFormPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'CAMPOSAREASDESCRIPCION_ID' => new sfWidgetFormInputHidden(),
      'AREADESCRIPCION_ID'        => new sfWidgetFormPropelChoice(array('model' => 'AreasDescripcion', 'add_empty' => false)),
      'DESCRIPCION_CAMPO'         => new sfWidgetFormInputText(),
      'ES_OBLIGATORIO'            => new sfWidgetFormInputText(),
    ));

    $this->setValidators(array(
      'CAMPOSAREASDESCRIPCION_ID' => new sfValidatorChoice(array('choices' => array($this->getObject()->getCamposareasdescripcionId()), 'empty_value' => $this->getObject()->getCamposareasdescripcionId(), 'required' => false)),
      'AREADESCRIPCION_ID'        => new sfValidatorPropelChoice(array('model' => 'AreasDescripcion', 'column' => 'AREADESCRIPCION_ID')),
      'DESCRIPCION_CAMPO'         => new sfValidatorString(array('max_length' => 250)),
      'ES_OBLIGATORIO'            => new sfValidatorInteger(array('min' => -128, 'max' => 127)),
    ));

    $this->widgetSchema->setNameFormat('campos_areas_descripcion[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'CamposAreasDescripcion';
  }


}
