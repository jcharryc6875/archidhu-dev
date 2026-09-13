<?php

/**
 * EspecialidadRegistro filter form base class.
 *
 * @package    simad
 * @subpackage filter
 * @author     Your name here
 */
abstract class BaseEspecialidadRegistroFormFilter extends BaseFormFilterPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'DESCRIPCION'             => new sfWidgetFormFilterInput(),
    ));

    $this->setValidators(array(
      'DESCRIPCION'             => new sfValidatorPass(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('especialidad_registro_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'EspecialidadRegistro';
  }

  public function getFields()
  {
    return array(
      'ESPECIALIDADREGISTRO_ID' => 'Number',
      'DESCRIPCION'             => 'Text',
    );
  }
}
