<?php

/**
 * EstadoAsignacionPqr filter form base class.
 *
 * @package    simad
 * @subpackage filter
 * @author     Your name here
 */
abstract class BaseEstadoAsignacionPqrFormFilter extends BaseFormFilterPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'DESCRIPCION'            => new sfWidgetFormFilterInput(),
    ));

    $this->setValidators(array(
      'DESCRIPCION'            => new sfValidatorPass(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('estado_asignacion_pqr_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'EstadoAsignacionPqr';
  }

  public function getFields()
  {
    return array(
      'ESTADOASIGNACIONPQR_ID' => 'Number',
      'DESCRIPCION'            => 'Text',
    );
  }
}
