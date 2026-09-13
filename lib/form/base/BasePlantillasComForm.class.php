<?php

/**
 * PlantillasCom form base class.
 *
 * @method PlantillasCom getObject() Returns the current form's model object
 *
 * @package    ##PROJECT_NAME##
 * @subpackage form
 * @author     ##AUTHOR_NAME##
 */
abstract class BasePlantillasComForm extends BaseFormPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'PLANTILLASCOM_ID' => new sfWidgetFormInputHidden(),
      'MODULO_ID'        => new sfWidgetFormPropelChoice(array('model' => 'Modulo', 'add_empty' => false)),
      'REGIONAL_ID'      => new sfWidgetFormPropelChoice(array('model' => 'Regional', 'add_empty' => true)),
      'CODIGO'           => new sfWidgetFormInputText(),
      'DESCRIPCION'      => new sfWidgetFormInputText(),
      'NOMBRE'           => new sfWidgetFormInputText(),
      'ES_ACTUAL'        => new sfWidgetFormInputText(),
      'CONTENTS'         => new sfWidgetFormTextarea(),
    ));

    $this->setValidators(array(
      'PLANTILLASCOM_ID' => new sfValidatorChoice(array('choices' => array($this->getObject()->getPlantillascomId()), 'empty_value' => $this->getObject()->getPlantillascomId(), 'required' => false)),
      'MODULO_ID'        => new sfValidatorPropelChoice(array('model' => 'Modulo', 'column' => 'MODULO_ID')),
      'REGIONAL_ID'      => new sfValidatorPropelChoice(array('model' => 'Regional', 'column' => 'REGIONAL_ID', 'required' => false)),
      'CODIGO'           => new sfValidatorString(array('max_length' => 30, 'required' => false)),
      'DESCRIPCION'      => new sfValidatorString(array('max_length' => 50)),
      'NOMBRE'           => new sfValidatorString(array('max_length' => 50, 'required' => false)),
      'ES_ACTUAL'        => new sfValidatorInteger(array('min' => -128, 'max' => 127, 'required' => false)),
      'CONTENTS'         => new sfValidatorString(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('plantillas_com[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'PlantillasCom';
  }


}
