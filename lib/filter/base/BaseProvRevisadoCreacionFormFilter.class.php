<?php

/**
 * ProvRevisadoCreacion filter form base class.
 *
 * @package    simad
 * @subpackage filter
 * @author     Your name here
 */
abstract class BaseProvRevisadoCreacionFormFilter extends BaseFormFilterPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'DESCRIPCION'               => new sfWidgetFormFilterInput(),
    ));

    $this->setValidators(array(
      'DESCRIPCION'               => new sfValidatorPass(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('prov_revisado_creacion_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'ProvRevisadoCreacion';
  }

  public function getFields()
  {
    return array(
      'PROV_REVISADO_CREACION_ID' => 'Number',
      'DESCRIPCION'               => 'Text',
    );
  }
}
