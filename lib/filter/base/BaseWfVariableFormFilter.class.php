<?php

/**
 * WfVariable filter form base class.
 *
 * @package    simad
 * @subpackage filter
 * @author     Your name here
 */
abstract class BaseWfVariableFormFilter extends BaseFormFilterPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'WFACTIVIDADTRANSICION_ID' => new sfWidgetFormPropelChoice(array('model' => 'WfActividadTransicion', 'add_empty' => true)),
      'WFTIPODATO_ID'            => new sfWidgetFormPropelChoice(array('model' => 'WfTipoDato', 'add_empty' => true)),
      'NOMBRE'                   => new sfWidgetFormFilterInput(),
      'ORDEN'                    => new sfWidgetFormFilterInput(),
    ));

    $this->setValidators(array(
      'WFACTIVIDADTRANSICION_ID' => new sfValidatorPropelChoice(array('required' => false, 'model' => 'WfActividadTransicion', 'column' => 'WFACTIVIDADTRANSICION_ID')),
      'WFTIPODATO_ID'            => new sfValidatorPropelChoice(array('required' => false, 'model' => 'WfTipoDato', 'column' => 'WFTIPODATO_ID')),
      'NOMBRE'                   => new sfValidatorPass(array('required' => false)),
      'ORDEN'                    => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
    ));

    $this->widgetSchema->setNameFormat('wf_variable_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'WfVariable';
  }

  public function getFields()
  {
    return array(
      'WFVARIABLE_ID'            => 'Number',
      'WFACTIVIDADTRANSICION_ID' => 'ForeignKey',
      'WFTIPODATO_ID'            => 'ForeignKey',
      'NOMBRE'                   => 'Text',
      'ORDEN'                    => 'Number',
    );
  }
}
