<?php

/**
 * ProvAprobacion filter form base class.
 *
 * @package    simad
 * @subpackage filter
 * @author     Your name here
 */
abstract class BaseProvAprobacionFormFilter extends BaseFormFilterPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'PROV_USUARIO_AREA_ID'      => new sfWidgetFormPropelChoice(array('model' => 'ProvUsuarioArea', 'add_empty' => true)),
      'PROV_PERIODO_VALIDEZ_ID'   => new sfWidgetFormPropelChoice(array('model' => 'ProvPeriodoValidez', 'add_empty' => true)),
      'PROV_ESTADO_APROBACION_ID' => new sfWidgetFormPropelChoice(array('model' => 'ProvEstadoAprobacion', 'add_empty' => true)),
      'FECHA_CREACION'            => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate())),
      'OBSERVACIONES'             => new sfWidgetFormFilterInput(),
      'USUARIO_ID'                => new sfWidgetFormFilterInput(),
    ));

    $this->setValidators(array(
      'PROV_USUARIO_AREA_ID'      => new sfValidatorPropelChoice(array('required' => false, 'model' => 'ProvUsuarioArea', 'column' => 'PROV_USUARIO_AREA_ID')),
      'PROV_PERIODO_VALIDEZ_ID'   => new sfValidatorPropelChoice(array('required' => false, 'model' => 'ProvPeriodoValidez', 'column' => 'PROV_PERIODO_VALIDEZ_ID')),
      'PROV_ESTADO_APROBACION_ID' => new sfValidatorPropelChoice(array('required' => false, 'model' => 'ProvEstadoAprobacion', 'column' => 'PROV_ESTADO_APROBACION_ID')),
      'FECHA_CREACION'            => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDate(array('required' => false)), 'to_date' => new sfValidatorDate(array('required' => false)))),
      'OBSERVACIONES'             => new sfValidatorPass(array('required' => false)),
      'USUARIO_ID'                => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
    ));

    $this->widgetSchema->setNameFormat('prov_aprobacion_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'ProvAprobacion';
  }

  public function getFields()
  {
    return array(
      'PROV_APROBACION_ID'        => 'Number',
      'PROV_USUARIO_AREA_ID'      => 'ForeignKey',
      'PROV_PERIODO_VALIDEZ_ID'   => 'ForeignKey',
      'PROV_ESTADO_APROBACION_ID' => 'ForeignKey',
      'FECHA_CREACION'            => 'Date',
      'OBSERVACIONES'             => 'Text',
      'USUARIO_ID'                => 'Number',
    );
  }
}
