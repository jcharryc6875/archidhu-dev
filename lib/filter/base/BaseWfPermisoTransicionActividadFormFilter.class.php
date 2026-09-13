<?php

/**
 * WfPermisoTransicionActividad filter form base class.
 *
 * @package    simad
 * @subpackage filter
 * @author     Your name here
 */
abstract class BaseWfPermisoTransicionActividadFormFilter extends BaseFormFilterPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'wf_actividad_id'                    => new sfWidgetFormPropelChoice(array('model' => 'WfActividad', 'add_empty' => true)),
      'wf_transicion_id'                   => new sfWidgetFormPropelChoice(array('model' => 'WfTransicion', 'add_empty' => true)),
    ));

    $this->setValidators(array(
      'wf_actividad_id'                    => new sfValidatorPropelChoice(array('required' => false, 'model' => 'WfActividad', 'column' => 'WF_ACTIVIDAD_ID')),
      'wf_transicion_id'                   => new sfValidatorPropelChoice(array('required' => false, 'model' => 'WfTransicion', 'column' => 'WF_TRANSICION_ID')),
    ));

    $this->widgetSchema->setNameFormat('wf_permiso_transicion_actividad_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'WfPermisoTransicionActividad';
  }

  public function getFields()
  {
    return array(
      'wf_permiso_transicion_actividad_id' => 'Number',
      'wf_actividad_id'                    => 'ForeignKey',
      'wf_transicion_id'                   => 'ForeignKey',
    );
  }
}
