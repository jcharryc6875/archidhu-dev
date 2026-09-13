<?php

/**
 * ProvEstadoAprobacion filter form base class.
 *
 * @package    simad
 * @subpackage filter
 * @author     Your name here
 */
abstract class BaseProvEstadoAprobacionFormFilter extends BaseFormFilterPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'DESCRIPCION'               => new sfWidgetFormFilterInput(),
    ));

    $this->setValidators(array(
      'DESCRIPCION'               => new sfValidatorPass(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('prov_estado_aprobacion_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'ProvEstadoAprobacion';
  }

  public function getFields()
  {
    return array(
      'PROV_ESTADO_APROBACION_ID' => 'Number',
      'DESCRIPCION'               => 'Text',
    );
  }
}
