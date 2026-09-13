<?php

/**
 * ProvCorrespondienteAutofacturacion filter form base class.
 *
 * @package    simad
 * @subpackage filter
 * @author     Your name here
 */
abstract class BaseProvCorrespondienteAutofacturacionFormFilter extends BaseFormFilterPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'CODIGO'                                  => new sfWidgetFormFilterInput(),
      'DESCRIPCION'                             => new sfWidgetFormFilterInput(),
    ));

    $this->setValidators(array(
      'CODIGO'                                  => new sfValidatorPass(array('required' => false)),
      'DESCRIPCION'                             => new sfValidatorPass(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('prov_correspondiente_autofacturacion_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'ProvCorrespondienteAutofacturacion';
  }

  public function getFields()
  {
    return array(
      'PROV_CORRESPONDIENTE_AUTOFACTURACION_ID' => 'Number',
      'CODIGO'                                  => 'Text',
      'DESCRIPCION'                             => 'Text',
    );
  }
}
