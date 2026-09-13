<?php

/**
 * TipoProcesoComunicacion filter form base class.
 *
 * @package    simad
 * @subpackage filter
 * @author     Your name here
 */
abstract class BaseTipoProcesoComunicacionFormFilter extends BaseFormFilterPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'TRAMITE_ESTADO_ID'          => new sfWidgetFormPropelChoice(array('model' => 'TramiteEstado', 'add_empty' => true)),
    ));

    $this->setValidators(array(
      'TRAMITE_ESTADO_ID'          => new sfValidatorPropelChoice(array('required' => false, 'model' => 'TramiteEstado', 'column' => 'TRAMITE_ESTADO_ID')),
    ));

    $this->widgetSchema->setNameFormat('tipo_proceso_comunicacion_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'TipoProcesoComunicacion';
  }

  public function getFields()
  {
    return array(
      'TIPOPROCESOCOMUNICACION_ID' => 'Number',
      'TRAMITE_ESTADO_ID'          => 'ForeignKey',
    );
  }
}
