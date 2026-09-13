<?php

/**
 * Periodo filter form base class.
 *
 * @package    simad
 * @subpackage filter
 * @author     Your name here
 */
abstract class BasePeriodoFormFilter extends BaseFormFilterPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'DESCRIPCION'       => new sfWidgetFormFilterInput(),
      'ES_PERIODO_ACTUAL' => new sfWidgetFormFilterInput(),
    ));

    $this->setValidators(array(
      'DESCRIPCION'       => new sfValidatorPass(array('required' => false)),
      'ES_PERIODO_ACTUAL' => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
    ));

    $this->widgetSchema->setNameFormat('periodo_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'Periodo';
  }

  public function getFields()
  {
    return array(
      'PERIODO_ID'        => 'Number',
      'DESCRIPCION'       => 'Text',
      'ES_PERIODO_ACTUAL' => 'Number',
    );
  }
}
