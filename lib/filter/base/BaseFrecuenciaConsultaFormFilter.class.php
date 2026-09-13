<?php

/**
 * FrecuenciaConsulta filter form base class.
 *
 * @package    simad
 * @subpackage filter
 * @author     Your name here
 */
abstract class BaseFrecuenciaConsultaFormFilter extends BaseFormFilterPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'DESCRIPCION'           => new sfWidgetFormFilterInput(),
    ));

    $this->setValidators(array(
      'DESCRIPCION'           => new sfValidatorPass(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('frecuencia_consulta_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'FrecuenciaConsulta';
  }

  public function getFields()
  {
    return array(
      'FRECUENCIACONSULTA_ID' => 'Number',
      'DESCRIPCION'           => 'Text',
    );
  }
}
