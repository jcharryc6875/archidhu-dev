<?php

/**
 * ProvCorrespondienteAutofacturacion form base class.
 *
 * @method ProvCorrespondienteAutofacturacion getObject() Returns the current form's model object
 *
 * @package    ##PROJECT_NAME##
 * @subpackage form
 * @author     ##AUTHOR_NAME##
 */
abstract class BaseProvCorrespondienteAutofacturacionForm extends BaseFormPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'PROV_CORRESPONDIENTE_AUTOFACTURACION_ID' => new sfWidgetFormInputHidden(),
      'CODIGO'                                  => new sfWidgetFormInputText(),
      'DESCRIPCION'                             => new sfWidgetFormInputText(),
    ));

    $this->setValidators(array(
      'PROV_CORRESPONDIENTE_AUTOFACTURACION_ID' => new sfValidatorChoice(array('choices' => array($this->getObject()->getProvCorrespondienteAutofacturacionId()), 'empty_value' => $this->getObject()->getProvCorrespondienteAutofacturacionId(), 'required' => false)),
      'CODIGO'                                  => new sfValidatorString(array('max_length' => 50, 'required' => false)),
      'DESCRIPCION'                             => new sfValidatorString(array('max_length' => 250, 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('prov_correspondiente_autofacturacion[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'ProvCorrespondienteAutofacturacion';
  }


}
