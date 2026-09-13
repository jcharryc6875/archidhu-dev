<?php

/**
 * WfActividadTransicion form base class.
 *
 * @package    form
 * @subpackage wf_actividad_transicion
 * @version    SVN: $Id: sfPropelFormGeneratedTemplate.php 15484 2009-02-13 13:13:51Z fabien $
 */
class BaseWfActividadTransicionForm extends BaseFormPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'wfactividadtransicion_id' => new sfWidgetFormInputHidden(),
      'wf_transicion_id'         => new sfWidgetFormPropelSelect(array('model' => 'WfTransicion', 'add_empty' => false)),
      'wf_actividad_id'          => new sfWidgetFormPropelSelect(array('model' => 'WfActividad', 'add_empty' => false)),
      'wf_flujo_id'              => new sfWidgetFormPropelSelect(array('model' => 'WfFlujo', 'add_empty' => false)),
      'wf_estado_id'             => new sfWidgetFormPropelSelect(array('model' => 'WfEstado', 'add_empty' => false)),
      'wftipoactividad_id'       => new sfWidgetFormPropelSelect(array('model' => 'WfTipoActividad', 'add_empty' => true)),
      'wfscript_id'              => new sfWidgetFormPropelSelect(array('model' => 'WfScript', 'add_empty' => true)),
      'es_destino'               => new sfWidgetFormInputCheckbox(),
      'orden'                    => new sfWidgetFormInputText(),
      'tiempo_limite'            => new sfWidgetFormInputText(),
      'requiere_cominterna'      => new sfWidgetFormInputText(),
      'requiere_comrecibida'     => new sfWidgetFormInputText(),
      'requiere_comenviada'      => new sfWidgetFormInputText(),
    ));

    $this->setValidators(array(
      'wfactividadtransicion_id' => new sfValidatorPropelChoice(array('model' => 'WfActividadTransicion', 'column' => 'wfactividadtransicion_id', 'required' => false)),
      'wf_transicion_id'         => new sfValidatorPropelChoice(array('model' => 'WfTransicion', 'column' => 'wf_transicion_id')),
      'wf_actividad_id'          => new sfValidatorPropelChoice(array('model' => 'WfActividad', 'column' => 'wf_actividad_id')),
      'wf_flujo_id'              => new sfValidatorPropelChoice(array('model' => 'WfFlujo', 'column' => 'wf_flujo_id')),
      'wf_estado_id'             => new sfValidatorPropelChoice(array('model' => 'WfEstado', 'column' => 'wf_estado_id')),
      'wftipoactividad_id'       => new sfValidatorPropelChoice(array('model' => 'WfTipoActividad', 'column' => 'wftipoactividad_id', 'required' => false)),
      'wfscript_id'              => new sfValidatorPropelChoice(array('model' => 'WfScript', 'column' => 'wfscript_id', 'required' => false)),
      'es_destino'               => new sfValidatorBoolean(array('required' => false)),
      'orden'                    => new sfValidatorInteger(array('required' => false)),
      'tiempo_limite'            => new sfValidatorInteger(array('required' => false)),
      'requiere_cominterna'      => new sfValidatorInteger(array('required' => false)),
      'requiere_comrecibida'     => new sfValidatorInteger(array('required' => false)),
      'requiere_comenviada'      => new sfValidatorInteger(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('wf_actividad_transicion[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'WfActividadTransicion';
  }


}
