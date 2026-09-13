<?php

/**
 * ServicioBitacora filter form base class.
 *
 * @package    simad
 * @subpackage filter
 * @author     Your name here
 */
abstract class BaseServicioBitacoraFormFilter extends BaseFormFilterPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'SERVICIO_ID'         => new sfWidgetFormPropelChoice(array('model' => 'Servicio', 'add_empty' => true)),
      'SERVICIOPROCESO_ID'  => new sfWidgetFormPropelChoice(array('model' => 'ServicioProceso', 'add_empty' => true)),
      'SERVICIOESTADO_ID'   => new sfWidgetFormPropelChoice(array('model' => 'ServicioEstado', 'add_empty' => true)),
      'USUARIOASIGNADO_ID'  => new sfWidgetFormPropelChoice(array('model' => 'Usuario', 'add_empty' => true)),
      'USUARIOENVIA_ID'     => new sfWidgetFormPropelChoice(array('model' => 'Usuario', 'add_empty' => true)),
      'FECHA_ASIGNACION'    => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate())),
      'FECHA_EJECUCION'     => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate())),
      'OBSERVACIONES'       => new sfWidgetFormFilterInput(),
      'FECHA_CREACION'      => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate())),
    ));

    $this->setValidators(array(
      'SERVICIO_ID'         => new sfValidatorPropelChoice(array('required' => false, 'model' => 'Servicio', 'column' => 'SERVICIO_ID')),
      'SERVICIOPROCESO_ID'  => new sfValidatorPropelChoice(array('required' => false, 'model' => 'ServicioProceso', 'column' => 'SERVICIOPROCESO_ID')),
      'SERVICIOESTADO_ID'   => new sfValidatorPropelChoice(array('required' => false, 'model' => 'ServicioEstado', 'column' => 'SERVICIOESTADO_ID')),
      'USUARIOASIGNADO_ID'  => new sfValidatorPropelChoice(array('required' => false, 'model' => 'Usuario', 'column' => 'USUARIO_ID')),
      'USUARIOENVIA_ID'     => new sfValidatorPropelChoice(array('required' => false, 'model' => 'Usuario', 'column' => 'USUARIO_ID')),
      'FECHA_ASIGNACION'    => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDate(array('required' => false)), 'to_date' => new sfValidatorDate(array('required' => false)))),
      'FECHA_EJECUCION'     => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDate(array('required' => false)), 'to_date' => new sfValidatorDate(array('required' => false)))),
      'OBSERVACIONES'       => new sfValidatorPass(array('required' => false)),
      'FECHA_CREACION'      => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDate(array('required' => false)), 'to_date' => new sfValidatorDate(array('required' => false)))),
    ));

    $this->widgetSchema->setNameFormat('servicio_bitacora_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'ServicioBitacora';
  }

  public function getFields()
  {
    return array(
      'SERVICIOBITACORA_ID' => 'Number',
      'SERVICIO_ID'         => 'ForeignKey',
      'SERVICIOPROCESO_ID'  => 'ForeignKey',
      'SERVICIOESTADO_ID'   => 'ForeignKey',
      'USUARIOASIGNADO_ID'  => 'ForeignKey',
      'USUARIOENVIA_ID'     => 'ForeignKey',
      'FECHA_ASIGNACION'    => 'Date',
      'FECHA_EJECUCION'     => 'Date',
      'OBSERVACIONES'       => 'Text',
      'FECHA_CREACION'      => 'Date',
    );
  }
}
