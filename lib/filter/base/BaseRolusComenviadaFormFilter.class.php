<?php

/**
 * RolusComenviada filter form base class.
 *
 * @package    simad
 * @subpackage filter
 * @author     Your name here
 */
abstract class BaseRolusComenviadaFormFilter extends BaseFormFilterPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'DESCRIPCION'        => new sfWidgetFormFilterInput(),
    ));

    $this->setValidators(array(
      'DESCRIPCION'        => new sfValidatorPass(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('rolus_comenviada_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'RolusComenviada';
  }

  public function getFields()
  {
    return array(
      'ROLUSCOMENVIADA_ID' => 'Number',
      'DESCRIPCION'        => 'Text',
    );
  }
}
