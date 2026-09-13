<?php

/**
 * WfInstanciaBitacora form base class.
 *
 * @package    form
 * @subpackage wf_instancia_bitacora
 * @version    SVN: $Id: sfPropelFormGeneratedTemplate.php 15484 2009-02-13 13:13:51Z fabien $
 */
class BaseWfInstanciaBitacoraForm extends BaseFormPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'wfinstanciabitacora_id'   => new sfWidgetFormInputHidden(),
      'usuario_id'               => new sfWidgetFormPropelSelect(array('model' => 'Usuario', 'add_empty' => false)),
      'comenviada_id'            => new sfWidgetFormPropelSelect(array('model' => 'ComEnviada', 'add_empty' => true)),
      'cominterna_id'            => new sfWidgetFormPropelSelect(array('model' => 'ComInterna', 'add_empty' => true)),
      'wfinstancia_id'           => new sfWidgetFormPropelSelect(array('model' => 'WfInstancia', 'add_empty' => false)),
      'wfactividadtransicion_id' => new sfWidgetFormPropelSelect(array('model' => 'WfActividadTransicion', 'add_empty' => false)),
      'comrecibida_id'           => new sfWidgetFormPropelSelect(array('model' => 'ComRecibida', 'add_empty' => true)),
      'fecha_i'                  => new sfWidgetFormDateTime(),
      'fecha_f'                  => new sfWidgetFormDateTime(),
      'observaciones'            => new sfWidgetFormInputText(),
      'error'                    => new sfWidgetFormInputText(),
      'es_actual'                => new sfWidgetFormInputCheckbox(),
      'fecha_limite'             => new sfWidgetFormDateTime(),
      'buzon'                    => new sfWidgetFormInputText(),
      'buzon_id'                 => new sfWidgetFormInputText(),
      'estado_actividad'         => new sfWidgetFormInputText(),
      'script'                   => new sfWidgetFormInputText(),
      'script_params'            => new sfWidgetFormInputText(),
      'valor_variables'          => new sfWidgetFormInputText(),
      'wf_nombre_variables'      => new sfWidgetFormInputText(),
    ));

    $this->setValidators(array(
      'wfinstanciabitacora_id'   => new sfValidatorPropelChoice(array('model' => 'WfInstanciaBitacora', 'column' => 'wfinstanciabitacora_id', 'required' => false)),
      'usuario_id'               => new sfValidatorPropelChoice(array('model' => 'Usuario', 'column' => 'usuario_id')),
      'comenviada_id'            => new sfValidatorPropelChoice(array('model' => 'ComEnviada', 'column' => 'comenviada_id', 'required' => false)),
      'cominterna_id'            => new sfValidatorPropelChoice(array('model' => 'ComInterna', 'column' => 'cominterna_id', 'required' => false)),
      'wfinstancia_id'           => new sfValidatorPropelChoice(array('model' => 'WfInstancia', 'column' => 'wfinstancia_id')),
      'wfactividadtransicion_id' => new sfValidatorPropelChoice(array('model' => 'WfActividadTransicion', 'column' => 'wfactividadtransicion_id')),
      'comrecibida_id'           => new sfValidatorPropelChoice(array('model' => 'ComRecibida', 'column' => 'comrecibida_id', 'required' => false)),
      'fecha_i'                  => new sfValidatorDateTime(array('required' => false)),
      'fecha_f'                  => new sfValidatorDateTime(array('required' => false)),
      'observaciones'            => new sfValidatorString(array('max_length' => 200, 'required' => false)),
      'error'                    => new sfValidatorString(array('max_length' => 300, 'required' => false)),
      'es_actual'                => new sfValidatorBoolean(array('required' => false)),
      'fecha_limite'             => new sfValidatorDateTime(array('required' => false)),
      'buzon'                    => new sfValidatorInteger(array('required' => false)),
      'buzon_id'                 => new sfValidatorInteger(array('required' => false)),
      'estado_actividad'         => new sfValidatorInteger(array('required' => false)),
      'script'                   => new sfValidatorString(array('max_length' => 300, 'required' => false)),
      'script_params'            => new sfValidatorString(array('max_length' => 300, 'required' => false)),
      'valor_variables'          => new sfValidatorString(array('max_length' => 1000, 'required' => false)),
      'wf_nombre_variables'      => new sfValidatorString(array('max_length' => 1000, 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('wf_instancia_bitacora[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'WfInstanciaBitacora';
  }


}
