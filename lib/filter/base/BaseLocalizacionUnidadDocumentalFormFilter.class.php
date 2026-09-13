<?php

/**
 * LocalizacionUnidadDocumental filter form base class.
 *
 * @package    simad
 * @subpackage filter
 * @author     Your name here
 */
abstract class BaseLocalizacionUnidadDocumentalFormFilter extends BaseFormFilterPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'DESCRIPCION'                     => new sfWidgetFormFilterInput(),
    ));

    $this->setValidators(array(
      'DESCRIPCION'                     => new sfValidatorPass(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('localizacion_unidad_documental_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'LocalizacionUnidadDocumental';
  }

  public function getFields()
  {
    return array(
      'LOCALIZACIONUNIDADDOCUMENTAL_ID' => 'Number',
      'DESCRIPCION'                     => 'Text',
    );
  }
}
