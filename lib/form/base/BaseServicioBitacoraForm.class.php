<?php

/**
 * ServicioBitacora form base class.
 *
 * @method ServicioBitacora getObject() Returns the current form's model object
 *
 * @package    ##PROJECT_NAME##
 * @subpackage form
 * @author     ##AUTHOR_NAME##
 */
abstract class BaseServicioBitacoraForm extends BaseFormPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'SERVICIOBITACORA_ID' => new sfWidgetFormInputHidden(),
      'SERVICIO_ID'         => new sfWidgetFormPropelChoice(array('model' => 'Servicio', 'add_empty' => false)),
      'SERVICIOPROCESO_ID'  => new sfWidgetFormPropelChoice(array('model' => 'ServicioProceso', 'add_empty' => true)),
      'SERVICIOESTADO_ID'   => new sfWidgetFormPropelChoice(array('model' => 'ServicioEstado', 'add_empty' => false)),
      'USUARIOASIGNADO_ID'  => new sfWidgetFormPropelChoice(array('model' => 'Usuario', 'add_empty' => false)),
      'USUARIOENVIA_ID'     => new sfWidgetFormPropelChoice(array('model' => 'Usuario', 'add_empty' => false)),
      'FECHA_ASIGNACION'    => new sfWidgetFormDateTime(),
      'FECHA_EJECUCION'     => new sfWidgetFormDateTime(),
      'OBSERVACIONES'       => new sfWidgetFormInputText(),
      'FECHA_CREACION'      => new sfWidgetFormDateTime(),
    ));

    $this->setValidators(array(
      'SERVICIOBITACORA_ID' => new sfValidatorChoice(array('choices' => array($this->getObject()->getServiciobitacoraId()), 'empty_value' => $this->getObject()->getServiciobitacoraId(), 'required' => false)),
      'SERVICIO_ID'         => new sfValidatorPropelChoice(array('model' => 'Servicio', 'column' => 'SERVICIO_ID')),
      'SERVICIOPROCESO_ID'  => new sfValidatorPropelChoice(array('model' => 'ServicioProceso', 'column' => 'SERVICIOPROCESO_ID', 'required' => false)),
      'SERVICIOESTADO_ID'   => new sfValidatorPropelChoice(array('model' => 'ServicioEstado', 'column' => 'SERVICIOESTADO_ID')),
      'USUARIOASIGNADO_ID'  => new sfValidatorPropelChoice(array('model' => 'Usuario', 'column' => 'USUARIO_ID')),
      'USUARIOENVIA_ID'     => new sfValidatorPropelChoice(array('model' => 'Usuario', 'column' => 'USUARIO_ID')),
      'FECHA_ASIGNACION'    => new sfValidatorDateTime(array('required' => false)),
      'FECHA_EJECUCION'     => new sfValidatorDateTime(array('required' => false)),
      'OBSERVACIONES'       => new sfValidatorString(array('max_length' => 900, 'required' => false)),
      'FECHA_CREACION'      => new sfValidatorDateTime(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('servicio_bitacora[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'ServicioBitacora';
  }


}
