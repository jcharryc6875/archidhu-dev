<?php

/**
 * RolusuarioCominterna filter form base class.
 *
 * @package    simad
 * @subpackage filter
 * @author     Your name here
 */
abstract class BaseRolusuarioCominternaFormFilter extends BaseFormFilterPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'DESCRIPCION'             => new sfWidgetFormFilterInput(),
    ));

    $this->setValidators(array(
      'DESCRIPCION'             => new sfValidatorPass(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('rolusuario_cominterna_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'RolusuarioCominterna';
  }

  public function getFields()
  {
    return array(
      'ROLUSUARIOCOMINTERNA_ID' => 'Number',
      'DESCRIPCION'             => 'Text',
    );
  }
}
