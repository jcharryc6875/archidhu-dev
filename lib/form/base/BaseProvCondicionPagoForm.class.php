<?php

/**
 * ProvCondicionPago form base class.
 *
 * @method ProvCondicionPago getObject() Returns the current form's model object
 *
 * @package    ##PROJECT_NAME##
 * @subpackage form
 * @author     ##AUTHOR_NAME##
 */
abstract class BaseProvCondicionPagoForm extends BaseFormPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'PROV_CONDICION_PAGO_ID' => new sfWidgetFormInputHidden(),
      'DESCRIPCION'            => new sfWidgetFormInputText(),
      'CODIGO'                 => new sfWidgetFormInputText(),
    ));

    $this->setValidators(array(
      'PROV_CONDICION_PAGO_ID' => new sfValidatorChoice(array('choices' => array($this->getObject()->getProvCondicionPagoId()), 'empty_value' => $this->getObject()->getProvCondicionPagoId(), 'required' => false)),
      'DESCRIPCION'            => new sfValidatorString(array('max_length' => 250, 'required' => false)),
      'CODIGO'                 => new sfValidatorString(array('max_length' => 50, 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('prov_condicion_pago[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'ProvCondicionPago';
  }


}
