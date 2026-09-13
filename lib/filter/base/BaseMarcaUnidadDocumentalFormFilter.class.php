<?php

/**
 * MarcaUnidadDocumental filter form base class.
 *
 * @package    simad
 * @subpackage filter
 * @author     Your name here
 */
abstract class BaseMarcaUnidadDocumentalFormFilter extends BaseFormFilterPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'USUARIO_ID'               => new sfWidgetFormPropelChoice(array('model' => 'Usuario', 'add_empty' => true)),
      'UNIDADDOCUMENTAL_ID'      => new sfWidgetFormPropelChoice(array('model' => 'UnidadDocumental', 'add_empty' => true)),
    ));

    $this->setValidators(array(
      'USUARIO_ID'               => new sfValidatorPropelChoice(array('required' => false, 'model' => 'Usuario', 'column' => 'USUARIO_ID')),
      'UNIDADDOCUMENTAL_ID'      => new sfValidatorPropelChoice(array('required' => false, 'model' => 'UnidadDocumental', 'column' => 'UNIDADDOCUMENTAL_ID')),
    ));

    $this->widgetSchema->setNameFormat('marca_unidad_documental_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'MarcaUnidadDocumental';
  }

  public function getFields()
  {
    return array(
      'MARCAUNIDADDOCUMENTAL_ID' => 'Number',
      'USUARIO_ID'               => 'ForeignKey',
      'UNIDADDOCUMENTAL_ID'      => 'ForeignKey',
    );
  }
}
