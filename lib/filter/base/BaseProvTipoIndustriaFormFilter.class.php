<?php

/**
 * ProvTipoIndustria filter form base class.
 *
 * @package    simad
 * @subpackage filter
 * @author     Your name here
 */
abstract class BaseProvTipoIndustriaFormFilter extends BaseFormFilterPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'PAIS_ID'                => new sfWidgetFormPropelChoice(array('model' => 'Pais', 'add_empty' => true)),
      'DESCRIPCION'            => new sfWidgetFormFilterInput(),
      'CODIGO'                 => new sfWidgetFormFilterInput(),
    ));

    $this->setValidators(array(
      'PAIS_ID'                => new sfValidatorPropelChoice(array('required' => false, 'model' => 'Pais', 'column' => 'PAIS_ID')),
      'DESCRIPCION'            => new sfValidatorPass(array('required' => false)),
      'CODIGO'                 => new sfValidatorPass(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('prov_tipo_industria_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'ProvTipoIndustria';
  }

  public function getFields()
  {
    return array(
      'PROV_TIPO_INDUSTRIA_ID' => 'Number',
      'PAIS_ID'                => 'ForeignKey',
      'DESCRIPCION'            => 'Text',
      'CODIGO'                 => 'Text',
    );
  }
}
