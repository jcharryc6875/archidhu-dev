<?php

/**
 * PlantillasDetalle form base class.
 *
 * @method PlantillasDetalle getObject() Returns the current form's model object
 *
 * @package    ##PROJECT_NAME##
 * @subpackage form
 * @author     ##AUTHOR_NAME##
 */
abstract class BasePlantillasDetalleForm extends BaseFormPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'PLANTILLASDETALLE_ID' => new sfWidgetFormInputHidden(),
      'CODIGO'               => new sfWidgetFormInputText(),
      'DESCRIPCION'          => new sfWidgetFormInputText(),
      'HTML_DATA'            => new sfWidgetFormTextarea(),
      'ES_VISIBLE'           => new sfWidgetFormInputText(),
    ));

    $this->setValidators(array(
      'PLANTILLASDETALLE_ID' => new sfValidatorChoice(array('choices' => array($this->getObject()->getPlantillasdetalleId()), 'empty_value' => $this->getObject()->getPlantillasdetalleId(), 'required' => false)),
      'CODIGO'               => new sfValidatorString(array('max_length' => 30)),
      'DESCRIPCION'          => new sfValidatorString(array('max_length' => 250, 'required' => false)),
      'HTML_DATA'            => new sfValidatorString(array('required' => false)),
      'ES_VISIBLE'           => new sfValidatorInteger(array('min' => -2147483648, 'max' => 2147483647)),
    ));

    $this->widgetSchema->setNameFormat('plantillas_detalle[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'PlantillasDetalle';
  }


}
