<?php

/**
 * ProvEstadoAprobacion form base class.
 *
 * @method ProvEstadoAprobacion getObject() Returns the current form's model object
 *
 * @package    ##PROJECT_NAME##
 * @subpackage form
 * @author     ##AUTHOR_NAME##
 */
abstract class BaseProvEstadoAprobacionForm extends BaseFormPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'PROV_ESTADO_APROBACION_ID' => new sfWidgetFormInputHidden(),
      'DESCRIPCION'               => new sfWidgetFormInputText(),
    ));

    $this->setValidators(array(
      'PROV_ESTADO_APROBACION_ID' => new sfValidatorChoice(array('choices' => array($this->getObject()->getProvEstadoAprobacionId()), 'empty_value' => $this->getObject()->getProvEstadoAprobacionId(), 'required' => false)),
      'DESCRIPCION'               => new sfValidatorString(array('max_length' => 250, 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('prov_estado_aprobacion[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'ProvEstadoAprobacion';
  }


}
