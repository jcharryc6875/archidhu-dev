<?php

/**
 * WfInstancia form base class.
 *
 * @package    form
 * @subpackage wf_instancia
 * @version    SVN: $Id: sfPropelFormGeneratedTemplate.php 15484 2009-02-13 13:13:51Z fabien $
 */
class BaseWfInstanciaForm extends BaseFormPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'wfinstancia_id'           => new sfWidgetFormInputHidden(),
      'wfactividadtransicion_id' => new sfWidgetFormPropelSelect(array('model' => 'WfActividadTransicion', 'add_empty' => false)),
      'usuario_id'               => new sfWidgetFormPropelSelect(array('model' => 'Usuario', 'add_empty' => false)),
      'cominterna_id'            => new sfWidgetFormPropelSelect(array('model' => 'ComInterna', 'add_empty' => true)),
      'comrecibida_id'           => new sfWidgetFormPropelSelect(array('model' => 'ComRecibida', 'add_empty' => true)),
      'wf_flujo_id'              => new sfWidgetFormPropelSelect(array('model' => 'WfFlujo', 'add_empty' => false)),
      'esta_abierta'             => new sfWidgetFormInputCheckbox(),
      'fecha_i'                  => new sfWidgetFormDateTime(),
      'fecha_f'                  => new sfWidgetFormDateTime(),
      'fecha_ultima_actividad'   => new sfWidgetFormDateTime(),
      'observaciones'            => new sfWidgetFormInputText(),
    ));

    $this->setValidators(array(
      'wfinstancia_id'           => new sfValidatorPropelChoice(array('model' => 'WfInstancia', 'column' => 'wfinstancia_id', 'required' => false)),
      'wfactividadtransicion_id' => new sfValidatorPropelChoice(array('model' => 'WfActividadTransicion', 'column' => 'wfactividadtransicion_id')),
      'usuario_id'               => new sfValidatorPropelChoice(array('model' => 'Usuario', 'column' => 'usuario_id')),
      'cominterna_id'            => new sfValidatorPropelChoice(array('model' => 'ComInterna', 'column' => 'cominterna_id', 'required' => false)),
      'comrecibida_id'           => new sfValidatorPropelChoice(array('model' => 'ComRecibida', 'column' => 'comrecibida_id', 'required' => false)),
      'wf_flujo_id'              => new sfValidatorPropelChoice(array('model' => 'WfFlujo', 'column' => 'wf_flujo_id')),
      'esta_abierta'             => new sfValidatorBoolean(array('required' => false)),
      'fecha_i'                  => new sfValidatorDateTime(array('required' => false)),
      'fecha_f'                  => new sfValidatorDateTime(array('required' => false)),
      'fecha_ultima_actividad'   => new sfValidatorDateTime(array('required' => false)),
      'observaciones'            => new sfValidatorString(array('max_length' => 200, 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('wf_instancia[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'WfInstancia';
  }


}
