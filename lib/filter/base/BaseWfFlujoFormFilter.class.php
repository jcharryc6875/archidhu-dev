<?php

/**
 * WfFlujo filter form base class.
 *
 * @package    simad
 * @subpackage filter
 * @author     Your name here
 */
abstract class BaseWfFlujoFormFilter extends BaseFormFilterPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'TIPOCOMRECIBIDA_ID' => new sfWidgetFormPropelChoice(array('model' => 'TipoComRecibida', 'add_empty' => true)),
      'TIPOCOMINTERNA_ID'  => new sfWidgetFormPropelChoice(array('model' => 'TipoComInterna', 'add_empty' => true)),
      'WF_BUZON_ID'        => new sfWidgetFormPropelChoice(array('model' => 'WfBuzon', 'add_empty' => true)),
      'DESCRIPCION'        => new sfWidgetFormFilterInput(),
      'REGIONAL_ID'        => new sfWidgetFormPropelChoice(array('model' => 'Regional', 'add_empty' => true)),
      'ESTA_ACTIVO'        => new sfWidgetFormFilterInput(),
    ));

    $this->setValidators(array(
      'TIPOCOMRECIBIDA_ID' => new sfValidatorPropelChoice(array('required' => false, 'model' => 'TipoComRecibida', 'column' => 'TIPOCOMRECIBIDA_ID')),
      'TIPOCOMINTERNA_ID'  => new sfValidatorPropelChoice(array('required' => false, 'model' => 'TipoComInterna', 'column' => 'TIPOCOMINTERNA_ID')),
      'WF_BUZON_ID'        => new sfValidatorPropelChoice(array('required' => false, 'model' => 'WfBuzon', 'column' => 'WF_BUZON_ID')),
      'DESCRIPCION'        => new sfValidatorPass(array('required' => false)),
      'REGIONAL_ID'        => new sfValidatorPropelChoice(array('required' => false, 'model' => 'Regional', 'column' => 'REGIONAL_ID')),
      'ESTA_ACTIVO'        => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
    ));

    $this->widgetSchema->setNameFormat('wf_flujo_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'WfFlujo';
  }

  public function getFields()
  {
    return array(
      'WF_FLUJO_ID'        => 'Number',
      'TIPOCOMRECIBIDA_ID' => 'ForeignKey',
      'TIPOCOMINTERNA_ID'  => 'ForeignKey',
      'WF_BUZON_ID'        => 'ForeignKey',
      'DESCRIPCION'        => 'Text',
      'REGIONAL_ID'        => 'ForeignKey',
      'ESTA_ACTIVO'        => 'Number',
    );
  }
}
