<?php

/**
 * ProvCheckListVigencia filter form base class.
 *
 * @package    simad
 * @subpackage filter
 * @author     Your name here
 */
abstract class BaseProvCheckListVigenciaFormFilter extends BaseFormFilterPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'PROV_PERIODO_VALIDEZ_ID'     => new sfWidgetFormPropelChoice(array('model' => 'ProvPeriodoValidez', 'add_empty' => true)),
      'PROV_CHECK_LIST_PREGUNTA_ID' => new sfWidgetFormPropelChoice(array('model' => 'ProvCheckListPregunta', 'add_empty' => true)),
      'DESCRIPCION'                 => new sfWidgetFormFilterInput(),
      'RESPUESTA'                   => new sfWidgetFormFilterInput(),
      'OBSERVACIONES'               => new sfWidgetFormFilterInput(),
    ));

    $this->setValidators(array(
      'PROV_PERIODO_VALIDEZ_ID'     => new sfValidatorPropelChoice(array('required' => false, 'model' => 'ProvPeriodoValidez', 'column' => 'PROV_PERIODO_VALIDEZ_ID')),
      'PROV_CHECK_LIST_PREGUNTA_ID' => new sfValidatorPropelChoice(array('required' => false, 'model' => 'ProvCheckListPregunta', 'column' => 'PROV_CHECK_LIST_PREGUNTA_ID')),
      'DESCRIPCION'                 => new sfValidatorPass(array('required' => false)),
      'RESPUESTA'                   => new sfValidatorPass(array('required' => false)),
      'OBSERVACIONES'               => new sfValidatorPass(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('prov_check_list_vigencia_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'ProvCheckListVigencia';
  }

  public function getFields()
  {
    return array(
      'PROV_CHECK_LIST_VIGENCIA_ID' => 'Number',
      'PROV_PERIODO_VALIDEZ_ID'     => 'ForeignKey',
      'PROV_CHECK_LIST_PREGUNTA_ID' => 'ForeignKey',
      'DESCRIPCION'                 => 'Text',
      'RESPUESTA'                   => 'Text',
      'OBSERVACIONES'               => 'Text',
    );
  }
}
