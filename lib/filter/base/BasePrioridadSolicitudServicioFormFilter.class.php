<?php

/**
 * PrioridadSolicitudServicio filter form base class.
 *
 * @package    simad
 * @subpackage filter
 * @author     Your name here
 */
abstract class BasePrioridadSolicitudServicioFormFilter extends BaseFormFilterPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'DESCRIPCION'                   => new sfWidgetFormFilterInput(),
      'ES_VISIBLE'                    => new sfWidgetFormFilterInput(),
    ));

    $this->setValidators(array(
      'DESCRIPCION'                   => new sfValidatorPass(array('required' => false)),
      'ES_VISIBLE'                    => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
    ));

    $this->widgetSchema->setNameFormat('prioridad_solicitud_servicio_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'PrioridadSolicitudServicio';
  }

  public function getFields()
  {
    return array(
      'PRIORIDADSOLICITUDSERVICIO_ID' => 'Number',
      'DESCRIPCION'                   => 'Text',
      'ES_VISIBLE'                    => 'Number',
    );
  }
}
