<?php

/**
 * CamposAreasDescripcion filter form base class.
 *
 * @package    simad
 * @subpackage filter
 * @author     Your name here
 */
abstract class BaseCamposAreasDescripcionFormFilter extends BaseFormFilterPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'AREADESCRIPCION_ID'        => new sfWidgetFormPropelChoice(array('model' => 'AreasDescripcion', 'add_empty' => true)),
      'DESCRIPCION_CAMPO'         => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'ES_OBLIGATORIO'            => new sfWidgetFormFilterInput(array('with_empty' => false)),
    ));

    $this->setValidators(array(
      'AREADESCRIPCION_ID'        => new sfValidatorPropelChoice(array('required' => false, 'model' => 'AreasDescripcion', 'column' => 'AREADESCRIPCION_ID')),
      'DESCRIPCION_CAMPO'         => new sfValidatorPass(array('required' => false)),
      'ES_OBLIGATORIO'            => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
    ));

    $this->widgetSchema->setNameFormat('campos_areas_descripcion_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'CamposAreasDescripcion';
  }

  public function getFields()
  {
    return array(
      'CAMPOSAREASDESCRIPCION_ID' => 'Number',
      'AREADESCRIPCION_ID'        => 'ForeignKey',
      'DESCRIPCION_CAMPO'         => 'Text',
      'ES_OBLIGATORIO'            => 'Number',
    );
  }
}
