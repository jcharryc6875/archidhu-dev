<?php

/**
 * ManualFormas filter form base class.
 *
 * @package    simad
 * @subpackage filter
 * @author     Your name here
 */
abstract class BaseManualFormasFormFilter extends BaseFormFilterPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'TIPOMANUALFORMA_ID' => new sfWidgetFormPropelChoice(array('model' => 'TipoManualForma', 'add_empty' => true)),
    ));

    $this->setValidators(array(
      'TIPOMANUALFORMA_ID' => new sfValidatorPropelChoice(array('required' => false, 'model' => 'TipoManualForma', 'column' => 'TIPOMANUALFORMA_ID')),
    ));

    $this->widgetSchema->setNameFormat('manual_formas_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'ManualFormas';
  }

  public function getFields()
  {
    return array(
      'MANUALFORMAS_ID'    => 'Number',
      'TIPOMANUALFORMA_ID' => 'ForeignKey',
    );
  }
}
