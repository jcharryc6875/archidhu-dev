<?php

/**
 * DestinoTransferencia form base class.
 *
 * @method DestinoTransferencia getObject() Returns the current form's model object
 *
 * @package    ##PROJECT_NAME##
 * @subpackage form
 * @author     ##AUTHOR_NAME##
 */
abstract class BaseDestinoTransferenciaForm extends BaseFormPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'DESTINOTRANSFERENCIA_ID' => new sfWidgetFormInputHidden(),
      'DESCRIPCION'             => new sfWidgetFormInputText(),
    ));

    $this->setValidators(array(
      'DESTINOTRANSFERENCIA_ID' => new sfValidatorChoice(array('choices' => array($this->getObject()->getDestinotransferenciaId()), 'empty_value' => $this->getObject()->getDestinotransferenciaId(), 'required' => false)),
      'DESCRIPCION'             => new sfValidatorString(array('max_length' => 250, 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('destino_transferencia[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'DestinoTransferencia';
  }


}
