<?php

/**
 * UsuarioAsignadoSolicitud filter form base class.
 *
 * @package    simad
 * @subpackage filter
 * @author     Your name here
 */
abstract class BaseUsuarioAsignadoSolicitudFormFilter extends BaseFormFilterPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'ROLUSUARIOASIGNACIONSERVICIO_ID' => new sfWidgetFormPropelChoice(array('model' => 'RolUsuarioAsignacionServicio', 'add_empty' => true)),
      'ASIGNARSERVICIO_ID'              => new sfWidgetFormPropelChoice(array('model' => 'AsignarServicio', 'add_empty' => true)),
      'USUARIO_ID'                      => new sfWidgetFormPropelChoice(array('model' => 'Usuario', 'add_empty' => true)),
      'ESTA_ASIGNADO'                   => new sfWidgetFormFilterInput(),
    ));

    $this->setValidators(array(
      'ROLUSUARIOASIGNACIONSERVICIO_ID' => new sfValidatorPropelChoice(array('required' => false, 'model' => 'RolUsuarioAsignacionServicio', 'column' => 'ROLUSUARIOASIGNACIONSERVICIO_ID')),
      'ASIGNARSERVICIO_ID'              => new sfValidatorPropelChoice(array('required' => false, 'model' => 'AsignarServicio', 'column' => 'ASIGNARSERVICIO_ID')),
      'USUARIO_ID'                      => new sfValidatorPropelChoice(array('required' => false, 'model' => 'Usuario', 'column' => 'USUARIO_ID')),
      'ESTA_ASIGNADO'                   => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
    ));

    $this->widgetSchema->setNameFormat('usuario_asignado_solicitud_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'UsuarioAsignadoSolicitud';
  }

  public function getFields()
  {
    return array(
      'USUARIOASIGNADOSOLICITUD_ID'     => 'Number',
      'ROLUSUARIOASIGNACIONSERVICIO_ID' => 'ForeignKey',
      'ASIGNARSERVICIO_ID'              => 'ForeignKey',
      'USUARIO_ID'                      => 'ForeignKey',
      'ESTA_ASIGNADO'                   => 'Number',
    );
  }
}
