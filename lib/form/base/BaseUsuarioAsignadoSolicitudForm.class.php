<?php

/**
 * UsuarioAsignadoSolicitud form base class.
 *
 * @method UsuarioAsignadoSolicitud getObject() Returns the current form's model object
 *
 * @package    ##PROJECT_NAME##
 * @subpackage form
 * @author     ##AUTHOR_NAME##
 */
abstract class BaseUsuarioAsignadoSolicitudForm extends BaseFormPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'USUARIOASIGNADOSOLICITUD_ID'     => new sfWidgetFormInputHidden(),
      'ROLUSUARIOASIGNACIONSERVICIO_ID' => new sfWidgetFormPropelChoice(array('model' => 'RolUsuarioAsignacionServicio', 'add_empty' => false)),
      'ASIGNARSERVICIO_ID'              => new sfWidgetFormPropelChoice(array('model' => 'AsignarServicio', 'add_empty' => false)),
      'USUARIO_ID'                      => new sfWidgetFormPropelChoice(array('model' => 'Usuario', 'add_empty' => false)),
      'ESTA_ASIGNADO'                   => new sfWidgetFormInputText(),
    ));

    $this->setValidators(array(
      'USUARIOASIGNADOSOLICITUD_ID'     => new sfValidatorChoice(array('choices' => array($this->getObject()->getUsuarioasignadosolicitudId()), 'empty_value' => $this->getObject()->getUsuarioasignadosolicitudId(), 'required' => false)),
      'ROLUSUARIOASIGNACIONSERVICIO_ID' => new sfValidatorPropelChoice(array('model' => 'RolUsuarioAsignacionServicio', 'column' => 'ROLUSUARIOASIGNACIONSERVICIO_ID')),
      'ASIGNARSERVICIO_ID'              => new sfValidatorPropelChoice(array('model' => 'AsignarServicio', 'column' => 'ASIGNARSERVICIO_ID')),
      'USUARIO_ID'                      => new sfValidatorPropelChoice(array('model' => 'Usuario', 'column' => 'USUARIO_ID')),
      'ESTA_ASIGNADO'                   => new sfValidatorInteger(array('min' => -128, 'max' => 127, 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('usuario_asignado_solicitud[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'UsuarioAsignadoSolicitud';
  }


}
