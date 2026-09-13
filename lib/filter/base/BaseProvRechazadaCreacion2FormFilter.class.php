<?php

/**
 * ProvRechazadaCreacion2 filter form base class.
 *
 * @package    simad
 * @subpackage filter
 * @author     Your name here
 */
abstract class BaseProvRechazadaCreacion2FormFilter extends BaseFormFilterPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'DESCRIPCION'                 => new sfWidgetFormFilterInput(),
    ));

    $this->setValidators(array(
      'DESCRIPCION'                 => new sfValidatorPass(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('prov_rechazada_creacion2_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'ProvRechazadaCreacion2';
  }

  public function getFields()
  {
    return array(
      'PROV_RECHAZADA_CREACION2_ID' => 'Number',
      'DESCRIPCION'                 => 'Text',
    );
  }
}
