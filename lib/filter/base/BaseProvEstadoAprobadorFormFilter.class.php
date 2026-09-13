<?php

/**
 * ProvEstadoAprobador filter form base class.
 *
 * @package    simad
 * @subpackage filter
 * @author     Your name here
 */
abstract class BaseProvEstadoAprobadorFormFilter extends BaseFormFilterPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'DESCRIPCION'              => new sfWidgetFormFilterInput(),
    ));

    $this->setValidators(array(
      'DESCRIPCION'              => new sfValidatorPass(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('prov_estado_aprobador_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'ProvEstadoAprobador';
  }

  public function getFields()
  {
    return array(
      'PROV_ESTADO_APROBADOR_ID' => 'Number',
      'DESCRIPCION'              => 'Text',
    );
  }
}
