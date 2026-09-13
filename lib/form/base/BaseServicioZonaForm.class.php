<?php

/**
 * ServicioZona form base class.
 *
 * @method ServicioZona getObject() Returns the current form's model object
 *
 * @package    ##PROJECT_NAME##
 * @subpackage form
 * @author     ##AUTHOR_NAME##
 */
abstract class BaseServicioZonaForm extends BaseFormPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'SERVICIOZONA_ID' => new sfWidgetFormInputHidden(),
      'DESCRIPCION'     => new sfWidgetFormInputText(),
      'ES_VISIBLE'      => new sfWidgetFormInputText(),
    ));

    $this->setValidators(array(
      'SERVICIOZONA_ID' => new sfValidatorChoice(array('choices' => array($this->getObject()->getServiciozonaId()), 'empty_value' => $this->getObject()->getServiciozonaId(), 'required' => false)),
      'DESCRIPCION'     => new sfValidatorString(array('max_length' => 50, 'required' => false)),
      'ES_VISIBLE'      => new sfValidatorInteger(array('min' => -128, 'max' => 127, 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('servicio_zona[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'ServicioZona';
  }


}
