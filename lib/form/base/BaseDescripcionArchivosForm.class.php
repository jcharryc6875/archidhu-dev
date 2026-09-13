<?php

/**
 * DescripcionArchivos form base class.
 *
 * @method DescripcionArchivos getObject() Returns the current form's model object
 *
 * @package    ##PROJECT_NAME##
 * @subpackage form
 * @author     ##AUTHOR_NAME##
 */
abstract class BaseDescripcionArchivosForm extends BaseFormPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'DESCRIPCIONARCHIVOS_ID'    => new sfWidgetFormInputHidden(),
      'UNIDADDOCUMENTAL_ID'       => new sfWidgetFormPropelChoice(array('model' => 'UnidadDocumental', 'add_empty' => false)),
      'NIVELDESCRIPCION_ID'       => new sfWidgetFormPropelChoice(array('model' => 'NivelDescripcion', 'add_empty' => false)),
      'CAMPOSAREASDESCRIPCION_ID' => new sfWidgetFormPropelChoice(array('model' => 'CamposAreasDescripcion', 'add_empty' => false)),
      'VALUE'                     => new sfWidgetFormInputText(),
    ));

    $this->setValidators(array(
      'DESCRIPCIONARCHIVOS_ID'    => new sfValidatorChoice(array('choices' => array($this->getObject()->getDescripcionarchivosId()), 'empty_value' => $this->getObject()->getDescripcionarchivosId(), 'required' => false)),
      'UNIDADDOCUMENTAL_ID'       => new sfValidatorPropelChoice(array('model' => 'UnidadDocumental', 'column' => 'UNIDADDOCUMENTAL_ID')),
      'NIVELDESCRIPCION_ID'       => new sfValidatorPropelChoice(array('model' => 'NivelDescripcion', 'column' => 'NIVELDESCRIPCION_ID')),
      'CAMPOSAREASDESCRIPCION_ID' => new sfValidatorPropelChoice(array('model' => 'CamposAreasDescripcion', 'column' => 'CAMPOSAREASDESCRIPCION_ID')),
      'VALUE'                     => new sfValidatorString(array('max_length' => 800, 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('descripcion_archivos[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'DescripcionArchivos';
  }


}
