<?php

/**
 * ClDestinoTransferencia form base class.
 *
 * @method ClDestinoTransferencia getObject() Returns the current form's model object
 *
 * @package    ##PROJECT_NAME##
 * @subpackage form
 * @author     ##AUTHOR_NAME##
 */
abstract class BaseClDestinoTransferenciaForm extends BaseFormPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'CLDESTINOTRANSFERENCIA_ID' => new sfWidgetFormInputHidden(),
      'DESCRIPCION'               => new sfWidgetFormInputText(),
    ));

    $this->setValidators(array(
      'CLDESTINOTRANSFERENCIA_ID' => new sfValidatorChoice(array('choices' => array($this->getObject()->getCldestinotransferenciaId()), 'empty_value' => $this->getObject()->getCldestinotransferenciaId(), 'required' => false)),
      'DESCRIPCION'               => new sfValidatorString(array('max_length' => 250, 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('cl_destino_transferencia[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'ClDestinoTransferencia';
  }


}
