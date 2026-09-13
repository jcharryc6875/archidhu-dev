<?php

/**
 * DescarteFinalSubserie filter form base class.
 *
 * @package    simad
 * @subpackage filter
 * @author     Your name here
 */
abstract class BaseDescarteFinalSubserieFormFilter extends BaseFormFilterPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'DESCRIPCION'              => new sfWidgetFormFilterInput(),
    ));

    $this->setValidators(array(
      'DESCRIPCION'              => new sfValidatorPass(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('descarte_final_subserie_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'DescarteFinalSubserie';
  }

  public function getFields()
  {
    return array(
      'DESCARTEFINALSUBSERIE_ID' => 'Number',
      'DESCRIPCION'              => 'Text',
    );
  }
}
