<?php

/**
 * TipoComInterna form base class.
 *
 * @method TipoComInterna getObject() Returns the current form's model object
 *
 * @package    ##PROJECT_NAME##
 * @subpackage form
 * @author     ##AUTHOR_NAME##
 */
abstract class BaseTipoComInternaForm extends BaseFormPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'TIPOCOMINTERNA_ID' => new sfWidgetFormInputHidden(),
      'PLANTILLASCOM_ID'  => new sfWidgetFormPropelChoice(array('model' => 'PlantillasCom', 'add_empty' => true)),
      'DESCRIPCION'       => new sfWidgetFormInputText(),
      'ES_VISIBLE'        => new sfWidgetFormInputText(),
    ));

    $this->setValidators(array(
      'TIPOCOMINTERNA_ID' => new sfValidatorChoice(array('choices' => array($this->getObject()->getTipocominternaId()), 'empty_value' => $this->getObject()->getTipocominternaId(), 'required' => false)),
      'PLANTILLASCOM_ID'  => new sfValidatorPropelChoice(array('model' => 'PlantillasCom', 'column' => 'PLANTILLASCOM_ID', 'required' => false)),
      'DESCRIPCION'       => new sfValidatorString(array('max_length' => 250, 'required' => false)),
      'ES_VISIBLE'        => new sfValidatorInteger(array('min' => -128, 'max' => 127, 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('tipo_com_interna[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'TipoComInterna';
  }


}
