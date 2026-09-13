<?php

/**
 * ProvEstado form base class.
 *
 * @method ProvEstado getObject() Returns the current form's model object
 *
 * @package    ##PROJECT_NAME##
 * @subpackage form
 * @author     ##AUTHOR_NAME##
 */
abstract class BaseProvEstadoForm extends BaseFormPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'PROV_ESTADO_ID' => new sfWidgetFormInputHidden(),
      'DESCRIPCION'    => new sfWidgetFormInputText(),
    ));

    $this->setValidators(array(
      'PROV_ESTADO_ID' => new sfValidatorChoice(array('choices' => array($this->getObject()->getProvEstadoId()), 'empty_value' => $this->getObject()->getProvEstadoId(), 'required' => false)),
      'DESCRIPCION'    => new sfValidatorString(array('max_length' => 250, 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('prov_estado[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'ProvEstado';
  }


}
