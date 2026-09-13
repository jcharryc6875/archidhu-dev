<?php

/**
 * AsignarServicio filter form base class.
 *
 * @package    simad
 * @subpackage filter
 * @author     Your name here
 */
abstract class BaseAsignarServicioFormFilter extends BaseFormFilterPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'SERVICIO_ID'            => new sfWidgetFormPropelChoice(array('model' => 'Servicio', 'add_empty' => true)),
      'OBSERVACIONES'          => new sfWidgetFormFilterInput(),
      'VALOR'                  => new sfWidgetFormFilterInput(),
      'FECHA_ASIGNACION'       => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate())),
      'FECHA_EJECUCION'        => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate())),
      'TEMP_USUARIOASIGNADO'   => new sfWidgetFormFilterInput(),
      'TEMP_REGIONAL'          => new sfWidgetFormFilterInput(),
      'TEMP_USUARIO_ASIGNADO2' => new sfWidgetFormFilterInput(),
      'TEMP_USUARIO_ASIGNA'    => new sfWidgetFormFilterInput(),
    ));

    $this->setValidators(array(
      'SERVICIO_ID'            => new sfValidatorPropelChoice(array('required' => false, 'model' => 'Servicio', 'column' => 'SERVICIO_ID')),
      'OBSERVACIONES'          => new sfValidatorPass(array('required' => false)),
      'VALOR'                  => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'FECHA_ASIGNACION'       => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDate(array('required' => false)), 'to_date' => new sfValidatorDate(array('required' => false)))),
      'FECHA_EJECUCION'        => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDate(array('required' => false)), 'to_date' => new sfValidatorDate(array('required' => false)))),
      'TEMP_USUARIOASIGNADO'   => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'TEMP_REGIONAL'          => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'TEMP_USUARIO_ASIGNADO2' => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'TEMP_USUARIO_ASIGNA'    => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
    ));

    $this->widgetSchema->setNameFormat('asignar_servicio_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'AsignarServicio';
  }

  public function getFields()
  {
    return array(
      'ASIGNARSERVICIO_ID'     => 'Number',
      'SERVICIO_ID'            => 'ForeignKey',
      'OBSERVACIONES'          => 'Text',
      'VALOR'                  => 'Number',
      'FECHA_ASIGNACION'       => 'Date',
      'FECHA_EJECUCION'        => 'Date',
      'TEMP_USUARIOASIGNADO'   => 'Number',
      'TEMP_REGIONAL'          => 'Number',
      'TEMP_USUARIO_ASIGNADO2' => 'Number',
      'TEMP_USUARIO_ASIGNA'    => 'Number',
    );
  }
}
