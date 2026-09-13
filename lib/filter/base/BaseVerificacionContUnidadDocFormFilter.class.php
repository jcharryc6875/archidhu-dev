<?php

/**
 * VerificacionContUnidadDoc filter form base class.
 *
 * @package    simad
 * @subpackage filter
 * @author     Your name here
 */
abstract class BaseVerificacionContUnidadDocFormFilter extends BaseFormFilterPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'DESCRIPCION'                  => new sfWidgetFormFilterInput(),
    ));

    $this->setValidators(array(
      'DESCRIPCION'                  => new sfValidatorPass(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('verificacion_cont_unidad_doc_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'VerificacionContUnidadDoc';
  }

  public function getFields()
  {
    return array(
      'VERIFICACIONCONTUNIDADDOC_ID' => 'Number',
      'DESCRIPCION'                  => 'Text',
    );
  }
}
