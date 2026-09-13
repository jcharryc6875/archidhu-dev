<?php

/**
 * ProvEstadoDoc form base class.
 *
 * @method ProvEstadoDoc getObject() Returns the current form's model object
 *
 * @package    ##PROJECT_NAME##
 * @subpackage form
 * @author     ##AUTHOR_NAME##
 */
abstract class BaseProvEstadoDocForm extends BaseFormPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'PROV_ESTADO_DOC_ID' => new sfWidgetFormInputHidden(),
      'DESCRIPCION'        => new sfWidgetFormInputText(),
    ));

    $this->setValidators(array(
      'PROV_ESTADO_DOC_ID' => new sfValidatorChoice(array('choices' => array($this->getObject()->getProvEstadoDocId()), 'empty_value' => $this->getObject()->getProvEstadoDocId(), 'required' => false)),
      'DESCRIPCION'        => new sfValidatorString(array('max_length' => 250, 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('prov_estado_doc[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'ProvEstadoDoc';
  }


}
