<?php

/**
 * WfActividadTransicion filter form base class.
 *
 * @package    simad
 * @subpackage filter
 * @author     Your name here
 */
abstract class BaseWfActividadTransicionFormFilter extends BaseFormFilterPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'WF_TRANSICION_ID'         => new sfWidgetFormPropelChoice(array('model' => 'WfTransicion', 'add_empty' => true)),
      'WF_ACTIVIDAD_ID'          => new sfWidgetFormPropelChoice(array('model' => 'WfActividad', 'add_empty' => true)),
      'WF_FLUJO_ID'              => new sfWidgetFormPropelChoice(array('model' => 'WfFlujo', 'add_empty' => true)),
      'WF_ESTADO_ID'             => new sfWidgetFormPropelChoice(array('model' => 'WfEstado', 'add_empty' => true)),
      'WFTIPOACTIVIDAD_ID'       => new sfWidgetFormPropelChoice(array('model' => 'WfTipoActividad', 'add_empty' => true)),
      'WFSCRIPT_ID'              => new sfWidgetFormPropelChoice(array('model' => 'WfScript', 'add_empty' => true)),
      'ES_DESTINO'               => new sfWidgetFormFilterInput(),
      'ORDEN'                    => new sfWidgetFormFilterInput(),
      'TIEMPO_LIMITE'            => new sfWidgetFormFilterInput(),
      'REQUIERE_COMINTERNA'      => new sfWidgetFormFilterInput(),
      'REQUIERE_COMRECIBIDA'     => new sfWidgetFormFilterInput(),
      'REQUIERE_COMENVIADA'      => new sfWidgetFormFilterInput(),
    ));

    $this->setValidators(array(
      'WF_TRANSICION_ID'         => new sfValidatorPropelChoice(array('required' => false, 'model' => 'WfTransicion', 'column' => 'WF_TRANSICION_ID')),
      'WF_ACTIVIDAD_ID'          => new sfValidatorPropelChoice(array('required' => false, 'model' => 'WfActividad', 'column' => 'WF_ACTIVIDAD_ID')),
      'WF_FLUJO_ID'              => new sfValidatorPropelChoice(array('required' => false, 'model' => 'WfFlujo', 'column' => 'WF_FLUJO_ID')),
      'WF_ESTADO_ID'             => new sfValidatorPropelChoice(array('required' => false, 'model' => 'WfEstado', 'column' => 'WF_ESTADO_ID')),
      'WFTIPOACTIVIDAD_ID'       => new sfValidatorPropelChoice(array('required' => false, 'model' => 'WfTipoActividad', 'column' => 'WFTIPOACTIVIDAD_ID')),
      'WFSCRIPT_ID'              => new sfValidatorPropelChoice(array('required' => false, 'model' => 'WfScript', 'column' => 'WFSCRIPT_ID')),
      'ES_DESTINO'               => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'ORDEN'                    => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'TIEMPO_LIMITE'            => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'REQUIERE_COMINTERNA'      => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'REQUIERE_COMRECIBIDA'     => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'REQUIERE_COMENVIADA'      => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
    ));

    $this->widgetSchema->setNameFormat('wf_actividad_transicion_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'WfActividadTransicion';
  }

  public function getFields()
  {
    return array(
      'WFACTIVIDADTRANSICION_ID' => 'Number',
      'WF_TRANSICION_ID'         => 'ForeignKey',
      'WF_ACTIVIDAD_ID'          => 'ForeignKey',
      'WF_FLUJO_ID'              => 'ForeignKey',
      'WF_ESTADO_ID'             => 'ForeignKey',
      'WFTIPOACTIVIDAD_ID'       => 'ForeignKey',
      'WFSCRIPT_ID'              => 'ForeignKey',
      'ES_DESTINO'               => 'Number',
      'ORDEN'                    => 'Number',
      'TIEMPO_LIMITE'            => 'Number',
      'REQUIERE_COMINTERNA'      => 'Number',
      'REQUIERE_COMRECIBIDA'     => 'Number',
      'REQUIERE_COMENVIADA'      => 'Number',
    );
  }
}
