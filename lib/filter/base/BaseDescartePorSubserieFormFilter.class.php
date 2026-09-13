<?php

/**
 * DescartePorSubserie filter form base class.
 *
 * @package    simad
 * @subpackage filter
 * @author     Your name here
 */
abstract class BaseDescartePorSubserieFormFilter extends BaseFormFilterPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'SUBSERIE_ID'              => new sfWidgetFormPropelChoice(array('model' => 'Subserie', 'add_empty' => true)),
      'DESCARTEFINALSUBSERIE_ID' => new sfWidgetFormPropelChoice(array('model' => 'DescarteFinalSubserie', 'add_empty' => true)),
    ));

    $this->setValidators(array(
      'SUBSERIE_ID'              => new sfValidatorPropelChoice(array('required' => false, 'model' => 'Subserie', 'column' => 'SUBSERIE_ID')),
      'DESCARTEFINALSUBSERIE_ID' => new sfValidatorPropelChoice(array('required' => false, 'model' => 'DescarteFinalSubserie', 'column' => 'DESCARTEFINALSUBSERIE_ID')),
    ));

    $this->widgetSchema->setNameFormat('descarte_por_subserie_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'DescartePorSubserie';
  }

  public function getFields()
  {
    return array(
      'DESCARTEPORSUBSERIE_ID'   => 'Number',
      'SUBSERIE_ID'              => 'ForeignKey',
      'DESCARTEFINALSUBSERIE_ID' => 'ForeignKey',
    );
  }
}
