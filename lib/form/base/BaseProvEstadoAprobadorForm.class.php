<?php

/**
 * ProvEstadoAprobador form base class.
 *
 * @method ProvEstadoAprobador getObject() Returns the current form's model object
 *
 * @package    ##PROJECT_NAME##
 * @subpackage form
 * @author     ##AUTHOR_NAME##
 */
abstract class BaseProvEstadoAprobadorForm extends BaseFormPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'PROV_ESTADO_APROBADOR_ID' => new sfWidgetFormInputHidden(),
      'DESCRIPCION'              => new sfWidgetFormInputText(),
    ));

    $this->setValidators(array(
      'PROV_ESTADO_APROBADOR_ID' => new sfValidatorChoice(array('choices' => array($this->getObject()->getProvEstadoAprobadorId()), 'empty_value' => $this->getObject()->getProvEstadoAprobadorId(), 'required' => false)),
      'DESCRIPCION'              => new sfValidatorString(array('max_length' => 250, 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('prov_estado_aprobador[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'ProvEstadoAprobador';
  }


}
