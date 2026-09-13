<?php

/**
 * ProvAreaAprobadora filter form base class.
 *
 * @package    simad
 * @subpackage filter
 * @author     Your name here
 */
abstract class BaseProvAreaAprobadoraFormFilter extends BaseFormFilterPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'NOMBRE'                  => new sfWidgetFormFilterInput(),
      'DESCRIPCION'             => new sfWidgetFormFilterInput(),
      'CODIGO'                  => new sfWidgetFormFilterInput(),
    ));

    $this->setValidators(array(
      'NOMBRE'                  => new sfValidatorPass(array('required' => false)),
      'DESCRIPCION'             => new sfValidatorPass(array('required' => false)),
      'CODIGO'                  => new sfValidatorPass(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('prov_area_aprobadora_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'ProvAreaAprobadora';
  }

  public function getFields()
  {
    return array(
      'PROV_AREA_APROBADORA_ID' => 'Number',
      'NOMBRE'                  => 'Text',
      'DESCRIPCION'             => 'Text',
      'CODIGO'                  => 'Text',
    );
  }
}
