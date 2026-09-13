<?php

/**
 * ProvAduanaEntrada form base class.
 *
 * @method ProvAduanaEntrada getObject() Returns the current form's model object
 *
 * @package    ##PROJECT_NAME##
 * @subpackage form
 * @author     ##AUTHOR_NAME##
 */
abstract class BaseProvAduanaEntradaForm extends BaseFormPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'PROV_ADUANA_ENTRADA_ID' => new sfWidgetFormInputHidden(),
      'CODIGO'                 => new sfWidgetFormInputText(),
      'DESCRIPCION'            => new sfWidgetFormInputText(),
    ));

    $this->setValidators(array(
      'PROV_ADUANA_ENTRADA_ID' => new sfValidatorChoice(array('choices' => array($this->getObject()->getProvAduanaEntradaId()), 'empty_value' => $this->getObject()->getProvAduanaEntradaId(), 'required' => false)),
      'CODIGO'                 => new sfValidatorString(array('max_length' => 50, 'required' => false)),
      'DESCRIPCION'            => new sfValidatorString(array('max_length' => 250, 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('prov_aduana_entrada[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'ProvAduanaEntrada';
  }


}
