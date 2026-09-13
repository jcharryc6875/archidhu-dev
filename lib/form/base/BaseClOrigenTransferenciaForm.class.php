<?php

/**
 * ClOrigenTransferencia form base class.
 *
 * @method ClOrigenTransferencia getObject() Returns the current form's model object
 *
 * @package    ##PROJECT_NAME##
 * @subpackage form
 * @author     ##AUTHOR_NAME##
 */
abstract class BaseClOrigenTransferenciaForm extends BaseFormPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'CLORIGENTRANSFERENCIA_ID' => new sfWidgetFormInputHidden(),
      'DESCRIPCION'              => new sfWidgetFormInputText(),
    ));

    $this->setValidators(array(
      'CLORIGENTRANSFERENCIA_ID' => new sfValidatorChoice(array('choices' => array($this->getObject()->getClorigentransferenciaId()), 'empty_value' => $this->getObject()->getClorigentransferenciaId(), 'required' => false)),
      'DESCRIPCION'              => new sfValidatorString(array('max_length' => 250, 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('cl_origen_transferencia[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'ClOrigenTransferencia';
  }


}
