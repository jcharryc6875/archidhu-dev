<?php

/**
 * ClEstadoContenido filter form base class.
 *
 * @package    simad
 * @subpackage filter
 * @author     Your name here
 */
abstract class BaseClEstadoContenidoFormFilter extends BaseFormFilterPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'DESCRIPCION'            => new sfWidgetFormFilterInput(),
    ));

    $this->setValidators(array(
      'DESCRIPCION'            => new sfValidatorPass(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('cl_estado_contenido_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'ClEstadoContenido';
  }

  public function getFields()
  {
    return array(
      'CL_ESTADO_CONTENIDO_ID' => 'Number',
      'DESCRIPCION'            => 'Text',
    );
  }
}
