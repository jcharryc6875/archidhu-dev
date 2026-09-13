<?php

/**
 * ProvRevisadoCreacion form base class.
 *
 * @method ProvRevisadoCreacion getObject() Returns the current form's model object
 *
 * @package    ##PROJECT_NAME##
 * @subpackage form
 * @author     ##AUTHOR_NAME##
 */
abstract class BaseProvRevisadoCreacionForm extends BaseFormPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'PROV_REVISADO_CREACION_ID' => new sfWidgetFormInputHidden(),
      'DESCRIPCION'               => new sfWidgetFormInputText(),
    ));

    $this->setValidators(array(
      'PROV_REVISADO_CREACION_ID' => new sfValidatorChoice(array('choices' => array($this->getObject()->getProvRevisadoCreacionId()), 'empty_value' => $this->getObject()->getProvRevisadoCreacionId(), 'required' => false)),
      'DESCRIPCION'               => new sfValidatorString(array('max_length' => 250, 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('prov_revisado_creacion[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'ProvRevisadoCreacion';
  }


}
