<?php

/**
 * ClienteEstado form base class.
 *
 * @method ClienteEstado getObject() Returns the current form's model object
 *
 * @package    ##PROJECT_NAME##
 * @subpackage form
 * @author     ##AUTHOR_NAME##
 */
abstract class BaseClienteEstadoForm extends BaseFormPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'CLIENTEESTADO_ID' => new sfWidgetFormInputHidden(),
      'DESCRIPCION'      => new sfWidgetFormInputText(),
    ));

    $this->setValidators(array(
      'CLIENTEESTADO_ID' => new sfValidatorChoice(array('choices' => array($this->getObject()->getClienteestadoId()), 'empty_value' => $this->getObject()->getClienteestadoId(), 'required' => false)),
      'DESCRIPCION'      => new sfValidatorString(array('max_length' => 250, 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('cliente_estado[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'ClienteEstado';
  }


}
