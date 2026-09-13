<?php

/**
 * TramiteEstado form base class.
 *
 * @method TramiteEstado getObject() Returns the current form's model object
 *
 * @package    ##PROJECT_NAME##
 * @subpackage form
 * @author     ##AUTHOR_NAME##
 */
abstract class BaseTramiteEstadoForm extends BaseFormPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'TRAMITE_ESTADO_ID' => new sfWidgetFormInputHidden(),
      'DESCRIPCION'       => new sfWidgetFormInputText(),
    ));

    $this->setValidators(array(
      'TRAMITE_ESTADO_ID' => new sfValidatorChoice(array('choices' => array($this->getObject()->getTramiteEstadoId()), 'empty_value' => $this->getObject()->getTramiteEstadoId(), 'required' => false)),
      'DESCRIPCION'       => new sfValidatorString(array('max_length' => 250, 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('tramite_estado[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'TramiteEstado';
  }


}
