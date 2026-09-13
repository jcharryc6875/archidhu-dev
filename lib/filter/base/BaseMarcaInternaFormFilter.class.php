<?php

/**
 * MarcaInterna filter form base class.
 *
 * @package    simad
 * @subpackage filter
 * @author     Your name here
 */
abstract class BaseMarcaInternaFormFilter extends BaseFormFilterPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'COMINTERNA_ID'   => new sfWidgetFormPropelChoice(array('model' => 'ComInterna', 'add_empty' => true)),
      'USUARIO_ID'      => new sfWidgetFormPropelChoice(array('model' => 'Usuario', 'add_empty' => true)),
    ));

    $this->setValidators(array(
      'COMINTERNA_ID'   => new sfValidatorPropelChoice(array('required' => false, 'model' => 'ComInterna', 'column' => 'COMINTERNA_ID')),
      'USUARIO_ID'      => new sfValidatorPropelChoice(array('required' => false, 'model' => 'Usuario', 'column' => 'USUARIO_ID')),
    ));

    $this->widgetSchema->setNameFormat('marca_interna_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'MarcaInterna';
  }

  public function getFields()
  {
    return array(
      'MARCAINTERNA_ID' => 'Number',
      'COMINTERNA_ID'   => 'ForeignKey',
      'USUARIO_ID'      => 'ForeignKey',
    );
  }
}
