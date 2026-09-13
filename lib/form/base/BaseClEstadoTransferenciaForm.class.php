<?php

/**
 * ClEstadoTransferencia form base class.
 *
 * @method ClEstadoTransferencia getObject() Returns the current form's model object
 *
 * @package    ##PROJECT_NAME##
 * @subpackage form
 * @author     ##AUTHOR_NAME##
 */
abstract class BaseClEstadoTransferenciaForm extends BaseFormPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'CLESTADOTRANSFERENCIA_ID' => new sfWidgetFormInputHidden(),
      'DESCRIPCION'              => new sfWidgetFormInputText(),
    ));

    $this->setValidators(array(
      'CLESTADOTRANSFERENCIA_ID' => new sfValidatorChoice(array('choices' => array($this->getObject()->getClestadotransferenciaId()), 'empty_value' => $this->getObject()->getClestadotransferenciaId(), 'required' => false)),
      'DESCRIPCION'              => new sfValidatorString(array('max_length' => 250, 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('cl_estado_transferencia[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'ClEstadoTransferencia';
  }


}
