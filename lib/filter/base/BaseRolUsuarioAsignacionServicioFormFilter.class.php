<?php

/**
 * RolUsuarioAsignacionServicio filter form base class.
 *
 * @package    simad
 * @subpackage filter
 * @author     Your name here
 */
abstract class BaseRolUsuarioAsignacionServicioFormFilter extends BaseFormFilterPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'DESCRIPCION'                     => new sfWidgetFormFilterInput(),
    ));

    $this->setValidators(array(
      'DESCRIPCION'                     => new sfValidatorPass(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('rol_usuario_asignacion_servicio_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'RolUsuarioAsignacionServicio';
  }

  public function getFields()
  {
    return array(
      'ROLUSUARIOASIGNACIONSERVICIO_ID' => 'Number',
      'DESCRIPCION'                     => 'Text',
    );
  }
}
