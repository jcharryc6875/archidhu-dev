<?php

/**
 * ManualTecnico form base class.
 *
 * @method ManualTecnico getObject() Returns the current form's model object
 *
 * @package    ##PROJECT_NAME##
 * @subpackage form
 * @author     ##AUTHOR_NAME##
 */
abstract class BaseManualTecnicoForm extends BaseFormPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'MANUALTECNICO_ID'       => new sfWidgetFormInputHidden(),
      'ESPECIALIDAD_ID'        => new sfWidgetFormPropelChoice(array('model' => 'Especialidad', 'add_empty' => false)),
      'DOCUMENTACION_ID'       => new sfWidgetFormPropelChoice(array('model' => 'Documentacion', 'add_empty' => false)),
      'MENCION_EDICION'        => new sfWidgetFormInputText(),
      'VERSION'                => new sfWidgetFormInputText(),
      'PAGINACION'             => new sfWidgetFormInputText(),
      'ILUSTRACIONES'          => new sfWidgetFormInputText(),
      'VOLUMEN'                => new sfWidgetFormInputText(),
      'MATERIAL_COMPLEMETARIO' => new sfWidgetFormInputText(),
      'ISBN'                   => new sfWidgetFormInputText(),
      'NOTAS_CONTENIDO'        => new sfWidgetFormInputText(),
    ));

    $this->setValidators(array(
      'MANUALTECNICO_ID'       => new sfValidatorChoice(array('choices' => array($this->getObject()->getManualtecnicoId()), 'empty_value' => $this->getObject()->getManualtecnicoId(), 'required' => false)),
      'ESPECIALIDAD_ID'        => new sfValidatorPropelChoice(array('model' => 'Especialidad', 'column' => 'ESPECIALIDAD_ID')),
      'DOCUMENTACION_ID'       => new sfValidatorPropelChoice(array('model' => 'Documentacion', 'column' => 'DOCUMENTACION_ID')),
      'MENCION_EDICION'        => new sfValidatorString(array('max_length' => 30, 'required' => false)),
      'VERSION'                => new sfValidatorNumber(array('required' => false)),
      'PAGINACION'             => new sfValidatorInteger(array('min' => -2147483648, 'max' => 2147483647, 'required' => false)),
      'ILUSTRACIONES'          => new sfValidatorInteger(array('min' => -128, 'max' => 127, 'required' => false)),
      'VOLUMEN'                => new sfValidatorNumber(array('required' => false)),
      'MATERIAL_COMPLEMETARIO' => new sfValidatorString(array('max_length' => 30, 'required' => false)),
      'ISBN'                   => new sfValidatorString(array('max_length' => 20, 'required' => false)),
      'NOTAS_CONTENIDO'        => new sfValidatorString(array('max_length' => 50, 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('manual_tecnico[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'ManualTecnico';
  }


}
