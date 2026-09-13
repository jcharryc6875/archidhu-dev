<?php

/**
 * OrigenTransferencia form base class.
 *
 * @method OrigenTransferencia getObject() Returns the current form's model object
 *
 * @package    ##PROJECT_NAME##
 * @subpackage form
 * @author     ##AUTHOR_NAME##
 */
abstract class BaseOrigenTransferenciaForm extends BaseFormPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'ORIGENTRANSFERENCIAID' => new sfWidgetFormInputHidden(),
      'DESCRIPCION'           => new sfWidgetFormInputText(),
    ));

    $this->setValidators(array(
      'ORIGENTRANSFERENCIAID' => new sfValidatorChoice(array('choices' => array($this->getObject()->getOrigentransferenciaid()), 'empty_value' => $this->getObject()->getOrigentransferenciaid(), 'required' => false)),
      'DESCRIPCION'           => new sfValidatorString(array('max_length' => 250, 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('origen_transferencia[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'OrigenTransferencia';
  }


}
