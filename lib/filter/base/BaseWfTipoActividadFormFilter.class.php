<?php

/**
 * WfTipoActividad filter form base class.
 *
 * @package    simad
 * @subpackage filter
 * @author     Your name here
 */
abstract class BaseWfTipoActividadFormFilter extends BaseFormFilterPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'DESCRIPCION'        => new sfWidgetFormFilterInput(),
    ));

    $this->setValidators(array(
      'DESCRIPCION'        => new sfValidatorPass(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('wf_tipo_actividad_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'WfTipoActividad';
  }

  public function getFields()
  {
    return array(
      'WFTIPOACTIVIDAD_ID' => 'Number',
      'DESCRIPCION'        => 'Text',
    );
  }
}
