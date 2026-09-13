<?php

/**
 * WfInstancia filter form base class.
 *
 * @package    simad
 * @subpackage filter
 * @author     Your name here
 */
abstract class BaseWfInstanciaFormFilter extends BaseFormFilterPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'WFACTIVIDADTRANSICION_ID' => new sfWidgetFormPropelChoice(array('model' => 'WfActividadTransicion', 'add_empty' => true)),
      'USUARIO_ID'               => new sfWidgetFormPropelChoice(array('model' => 'Usuario', 'add_empty' => true)),
      'COMINTERNA_ID'            => new sfWidgetFormPropelChoice(array('model' => 'ComInterna', 'add_empty' => true)),
      'COMRECIBIDA_ID'           => new sfWidgetFormPropelChoice(array('model' => 'ComRecibida', 'add_empty' => true)),
      'WF_FLUJO_ID'              => new sfWidgetFormPropelChoice(array('model' => 'WfFlujo', 'add_empty' => true)),
      'ESTA_ABIERTA'             => new sfWidgetFormFilterInput(),
      'FECHA_I'                  => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate())),
      'FECHA_F'                  => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate())),
      'FECHA_ULTIMA_ACTIVIDAD'   => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate())),
      'OBSERVACIONES'            => new sfWidgetFormFilterInput(),
    ));

    $this->setValidators(array(
      'WFACTIVIDADTRANSICION_ID' => new sfValidatorPropelChoice(array('required' => false, 'model' => 'WfActividadTransicion', 'column' => 'WFACTIVIDADTRANSICION_ID')),
      'USUARIO_ID'               => new sfValidatorPropelChoice(array('required' => false, 'model' => 'Usuario', 'column' => 'USUARIO_ID')),
      'COMINTERNA_ID'            => new sfValidatorPropelChoice(array('required' => false, 'model' => 'ComInterna', 'column' => 'COMINTERNA_ID')),
      'COMRECIBIDA_ID'           => new sfValidatorPropelChoice(array('required' => false, 'model' => 'ComRecibida', 'column' => 'COMRECIBIDA_ID')),
      'WF_FLUJO_ID'              => new sfValidatorPropelChoice(array('required' => false, 'model' => 'WfFlujo', 'column' => 'WF_FLUJO_ID')),
      'ESTA_ABIERTA'             => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'FECHA_I'                  => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDate(array('required' => false)), 'to_date' => new sfValidatorDate(array('required' => false)))),
      'FECHA_F'                  => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDate(array('required' => false)), 'to_date' => new sfValidatorDate(array('required' => false)))),
      'FECHA_ULTIMA_ACTIVIDAD'   => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDate(array('required' => false)), 'to_date' => new sfValidatorDate(array('required' => false)))),
      'OBSERVACIONES'            => new sfValidatorPass(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('wf_instancia_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'WfInstancia';
  }

  public function getFields()
  {
    return array(
      'WFINSTANCIA_ID'           => 'Number',
      'WFACTIVIDADTRANSICION_ID' => 'ForeignKey',
      'USUARIO_ID'               => 'ForeignKey',
      'COMINTERNA_ID'            => 'ForeignKey',
      'COMRECIBIDA_ID'           => 'ForeignKey',
      'WF_FLUJO_ID'              => 'ForeignKey',
      'ESTA_ABIERTA'             => 'Number',
      'FECHA_I'                  => 'Date',
      'FECHA_F'                  => 'Date',
      'FECHA_ULTIMA_ACTIVIDAD'   => 'Date',
      'OBSERVACIONES'            => 'Text',
    );
  }
}
