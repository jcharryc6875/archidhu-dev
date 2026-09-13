<?php

/**
 * FactVariable filter form base class.
 *
 * @package    simad
 * @subpackage filter
 * @author     Your name here
 */
abstract class BaseFactVariableFormFilter extends BaseFormFilterPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'FACTTIPODATO_ID' => new sfWidgetFormPropelChoice(array('model' => 'FacturaTipo', 'add_empty' => true)),
      'FACTURATIPO_ID'  => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'DESCRIPCION'     => new sfWidgetFormFilterInput(),
      'NOMBRE'          => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'ORDEN'           => new sfWidgetFormFilterInput(),
      'ES_REQUERIDO'    => new sfWidgetFormFilterInput(),
      'ES_ACTUAL'       => new sfWidgetFormFilterInput(),
      'APPLY_ALL'       => new sfWidgetFormFilterInput(),
    ));

    $this->setValidators(array(
      'FACTTIPODATO_ID' => new sfValidatorPropelChoice(array('required' => false, 'model' => 'FacturaTipo', 'column' => 'FACTURATIPO_ID')),
      'FACTURATIPO_ID'  => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'DESCRIPCION'     => new sfValidatorPass(array('required' => false)),
      'NOMBRE'          => new sfValidatorPass(array('required' => false)),
      'ORDEN'           => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'ES_REQUERIDO'    => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'ES_ACTUAL'       => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'APPLY_ALL'       => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
    ));

    $this->widgetSchema->setNameFormat('fact_variable_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'FactVariable';
  }

  public function getFields()
  {
    return array(
      'FACTVARIABLE_ID' => 'Number',
      'FACTTIPODATO_ID' => 'ForeignKey',
      'FACTURATIPO_ID'  => 'Number',
      'DESCRIPCION'     => 'Text',
      'NOMBRE'          => 'Text',
      'ORDEN'           => 'Number',
      'ES_REQUERIDO'    => 'Number',
      'ES_ACTUAL'       => 'Number',
      'APPLY_ALL'       => 'Number',
    );
  }
}
