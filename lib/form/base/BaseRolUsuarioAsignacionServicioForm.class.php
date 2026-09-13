<?php

/**
 * RolUsuarioAsignacionServicio form base class.
 *
 * @method RolUsuarioAsignacionServicio getObject() Returns the current form's model object
 *
 * @package    ##PROJECT_NAME##
 * @subpackage form
 * @author     ##AUTHOR_NAME##
 */
abstract class BaseRolUsuarioAsignacionServicioForm extends BaseFormPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'ROLUSUARIOASIGNACIONSERVICIO_ID' => new sfWidgetFormInputHidden(),
      'DESCRIPCION'                     => new sfWidgetFormInputText(),
    ));

    $this->setValidators(array(
      'ROLUSUARIOASIGNACIONSERVICIO_ID' => new sfValidatorChoice(array('choices' => array($this->getObject()->getRolusuarioasignacionservicioId()), 'empty_value' => $this->getObject()->getRolusuarioasignacionservicioId(), 'required' => false)),
      'DESCRIPCION'                     => new sfValidatorString(array('max_length' => 250, 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('rol_usuario_asignacion_servicio[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'RolUsuarioAsignacionServicio';
  }


}
