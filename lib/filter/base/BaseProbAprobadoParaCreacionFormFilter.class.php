<?php

/**
 * ProbAprobadoParaCreacion filter form base class.
 *
 * @package    simad
 * @subpackage filter
 * @author     Your name here
 */
abstract class BaseProbAprobadoParaCreacionFormFilter extends BaseFormFilterPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'DESCRIPCION'                    => new sfWidgetFormFilterInput(),
    ));

    $this->setValidators(array(
      'DESCRIPCION'                    => new sfValidatorPass(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('prob_aprobado_para_creacion_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'ProbAprobadoParaCreacion';
  }

  public function getFields()
  {
    return array(
      'PROB_APROBADO_PARA_CREACION_ID' => 'Number',
      'DESCRIPCION'                    => 'Text',
    );
  }
}
