<?php

/**
 * FrecConsultaCliente form base class.
 *
 * @method FrecConsultaCliente getObject() Returns the current form's model object
 *
 * @package    ##PROJECT_NAME##
 * @subpackage form
 * @author     ##AUTHOR_NAME##
 */
abstract class BaseFrecConsultaClienteForm extends BaseFormPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'FRECCONSULTACLIENTE_ID' => new sfWidgetFormInputHidden(),
      'DESCRIPCION'            => new sfWidgetFormInputText(),
    ));

    $this->setValidators(array(
      'FRECCONSULTACLIENTE_ID' => new sfValidatorChoice(array('choices' => array($this->getObject()->getFrecconsultaclienteId()), 'empty_value' => $this->getObject()->getFrecconsultaclienteId(), 'required' => false)),
      'DESCRIPCION'            => new sfValidatorString(array('max_length' => 250, 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('frec_consulta_cliente[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'FrecConsultaCliente';
  }


}
