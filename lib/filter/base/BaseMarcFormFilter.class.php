<?php

/**
 * Marc filter form base class.
 *
 * @package    simad
 * @subpackage filter
 * @author     Your name here
 */
abstract class BaseMarcFormFilter extends BaseFormFilterPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'CODIGO'      => new sfWidgetFormFilterInput(),
      'DESCRIPCION' => new sfWidgetFormFilterInput(),
    ));

    $this->setValidators(array(
      'CODIGO'      => new sfValidatorPass(array('required' => false)),
      'DESCRIPCION' => new sfValidatorPass(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('marc_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'Marc';
  }

  public function getFields()
  {
    return array(
      'MARC_ID'     => 'Number',
      'CODIGO'      => 'Text',
      'DESCRIPCION' => 'Text',
    );
  }
}
