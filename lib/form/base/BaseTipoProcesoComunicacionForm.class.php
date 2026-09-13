<?php

/**
 * TipoProcesoComunicacion form base class.
 *
 * @method TipoProcesoComunicacion getObject() Returns the current form's model object
 *
 * @package    ##PROJECT_NAME##
 * @subpackage form
 * @author     ##AUTHOR_NAME##
 */
abstract class BaseTipoProcesoComunicacionForm extends BaseFormPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'TIPOPROCESOCOMUNICACION_ID' => new sfWidgetFormInputHidden(),
      'TRAMITE_ESTADO_ID'          => new sfWidgetFormPropelChoice(array('model' => 'TramiteEstado', 'add_empty' => false)),
    ));

    $this->setValidators(array(
      'TIPOPROCESOCOMUNICACION_ID' => new sfValidatorChoice(array('choices' => array($this->getObject()->getTipoprocesocomunicacionId()), 'empty_value' => $this->getObject()->getTipoprocesocomunicacionId(), 'required' => false)),
      'TRAMITE_ESTADO_ID'          => new sfValidatorPropelChoice(array('model' => 'TramiteEstado', 'column' => 'TRAMITE_ESTADO_ID')),
    ));

    $this->widgetSchema->setNameFormat('tipo_proceso_comunicacion[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'TipoProcesoComunicacion';
  }


}
