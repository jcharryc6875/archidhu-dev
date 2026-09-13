<?php

/**
 * ProvRechazadaCreacion form base class.
 *
 * @method ProvRechazadaCreacion getObject() Returns the current form's model object
 *
 * @package    ##PROJECT_NAME##
 * @subpackage form
 * @author     ##AUTHOR_NAME##
 */
abstract class BaseProvRechazadaCreacionForm extends BaseFormPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'PROV_RECHAZADA_CREACION_ID' => new sfWidgetFormInputHidden(),
      'DESCRIPCION'                => new sfWidgetFormInputText(),
    ));

    $this->setValidators(array(
      'PROV_RECHAZADA_CREACION_ID' => new sfValidatorChoice(array('choices' => array($this->getObject()->getProvRechazadaCreacionId()), 'empty_value' => $this->getObject()->getProvRechazadaCreacionId(), 'required' => false)),
      'DESCRIPCION'                => new sfValidatorString(array('max_length' => 250, 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('prov_rechazada_creacion[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'ProvRechazadaCreacion';
  }


}
