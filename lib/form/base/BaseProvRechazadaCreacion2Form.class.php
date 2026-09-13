<?php

/**
 * ProvRechazadaCreacion2 form base class.
 *
 * @method ProvRechazadaCreacion2 getObject() Returns the current form's model object
 *
 * @package    ##PROJECT_NAME##
 * @subpackage form
 * @author     ##AUTHOR_NAME##
 */
abstract class BaseProvRechazadaCreacion2Form extends BaseFormPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'PROV_RECHAZADA_CREACION2_ID' => new sfWidgetFormInputHidden(),
      'DESCRIPCION'                 => new sfWidgetFormInputText(),
    ));

    $this->setValidators(array(
      'PROV_RECHAZADA_CREACION2_ID' => new sfValidatorChoice(array('choices' => array($this->getObject()->getProvRechazadaCreacion2Id()), 'empty_value' => $this->getObject()->getProvRechazadaCreacion2Id(), 'required' => false)),
      'DESCRIPCION'                 => new sfValidatorString(array('max_length' => 250, 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('prov_rechazada_creacion2[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'ProvRechazadaCreacion2';
  }


}
