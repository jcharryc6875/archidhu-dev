<?php

/**
 * VerificacionContUnidadDoc form base class.
 *
 * @method VerificacionContUnidadDoc getObject() Returns the current form's model object
 *
 * @package    ##PROJECT_NAME##
 * @subpackage form
 * @author     ##AUTHOR_NAME##
 */
abstract class BaseVerificacionContUnidadDocForm extends BaseFormPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'VERIFICACIONCONTUNIDADDOC_ID' => new sfWidgetFormInputHidden(),
      'DESCRIPCION'                  => new sfWidgetFormInputText(),
    ));

    $this->setValidators(array(
      'VERIFICACIONCONTUNIDADDOC_ID' => new sfValidatorChoice(array('choices' => array($this->getObject()->getVerificacioncontunidaddocId()), 'empty_value' => $this->getObject()->getVerificacioncontunidaddocId(), 'required' => false)),
      'DESCRIPCION'                  => new sfValidatorString(array('max_length' => 250, 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('verificacion_cont_unidad_doc[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'VerificacionContUnidadDoc';
  }


}
