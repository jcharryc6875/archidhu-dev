<?php

/**
 * ProvCheckListVigenciaHistorico filter form base class.
 *
 * @package    simad
 * @subpackage filter
 * @author     Your name here
 */
abstract class BaseProvCheckListVigenciaHistoricoFormFilter extends BaseFormFilterPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'PROV_CHECK_LIST_VIGENCIA_ID'           => new sfWidgetFormPropelChoice(array('model' => 'ProvCheckListVigencia', 'add_empty' => true)),
      'USUARIO_ID'                            => new sfWidgetFormPropelChoice(array('model' => 'Usuario', 'add_empty' => true)),
      'DESCRIPCION'                           => new sfWidgetFormFilterInput(),
      'RESPUESTA'                             => new sfWidgetFormFilterInput(),
      'OBSERVACIONES'                         => new sfWidgetFormFilterInput(),
      'FECHA_CREACION'                        => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate())),
    ));

    $this->setValidators(array(
      'PROV_CHECK_LIST_VIGENCIA_ID'           => new sfValidatorPropelChoice(array('required' => false, 'model' => 'ProvCheckListVigencia', 'column' => 'PROV_CHECK_LIST_VIGENCIA_ID')),
      'USUARIO_ID'                            => new sfValidatorPropelChoice(array('required' => false, 'model' => 'Usuario', 'column' => 'USUARIO_ID')),
      'DESCRIPCION'                           => new sfValidatorPass(array('required' => false)),
      'RESPUESTA'                             => new sfValidatorPass(array('required' => false)),
      'OBSERVACIONES'                         => new sfValidatorPass(array('required' => false)),
      'FECHA_CREACION'                        => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDate(array('required' => false)), 'to_date' => new sfValidatorDate(array('required' => false)))),
    ));

    $this->widgetSchema->setNameFormat('prov_check_list_vigencia_historico_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'ProvCheckListVigenciaHistorico';
  }

  public function getFields()
  {
    return array(
      'PROV_CHECK_LIST_VIGENCIA_HISTORICO_ID' => 'Number',
      'PROV_CHECK_LIST_VIGENCIA_ID'           => 'ForeignKey',
      'USUARIO_ID'                            => 'ForeignKey',
      'DESCRIPCION'                           => 'Text',
      'RESPUESTA'                             => 'Text',
      'OBSERVACIONES'                         => 'Text',
      'FECHA_CREACION'                        => 'Date',
    );
  }
}
