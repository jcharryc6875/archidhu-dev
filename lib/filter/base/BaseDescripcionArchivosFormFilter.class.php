<?php

/**
 * DescripcionArchivos filter form base class.
 *
 * @package    simad
 * @subpackage filter
 * @author     Your name here
 */
abstract class BaseDescripcionArchivosFormFilter extends BaseFormFilterPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'UNIDADDOCUMENTAL_ID'       => new sfWidgetFormPropelChoice(array('model' => 'UnidadDocumental', 'add_empty' => true)),
      'NIVELDESCRIPCION_ID'       => new sfWidgetFormPropelChoice(array('model' => 'NivelDescripcion', 'add_empty' => true)),
      'CAMPOSAREASDESCRIPCION_ID' => new sfWidgetFormPropelChoice(array('model' => 'CamposAreasDescripcion', 'add_empty' => true)),
      'VALUE'                     => new sfWidgetFormFilterInput(),
    ));

    $this->setValidators(array(
      'UNIDADDOCUMENTAL_ID'       => new sfValidatorPropelChoice(array('required' => false, 'model' => 'UnidadDocumental', 'column' => 'UNIDADDOCUMENTAL_ID')),
      'NIVELDESCRIPCION_ID'       => new sfValidatorPropelChoice(array('required' => false, 'model' => 'NivelDescripcion', 'column' => 'NIVELDESCRIPCION_ID')),
      'CAMPOSAREASDESCRIPCION_ID' => new sfValidatorPropelChoice(array('required' => false, 'model' => 'CamposAreasDescripcion', 'column' => 'CAMPOSAREASDESCRIPCION_ID')),
      'VALUE'                     => new sfValidatorPass(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('descripcion_archivos_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'DescripcionArchivos';
  }

  public function getFields()
  {
    return array(
      'DESCRIPCIONARCHIVOS_ID'    => 'Number',
      'UNIDADDOCUMENTAL_ID'       => 'ForeignKey',
      'NIVELDESCRIPCION_ID'       => 'ForeignKey',
      'CAMPOSAREASDESCRIPCION_ID' => 'ForeignKey',
      'VALUE'                     => 'Text',
    );
  }
}
