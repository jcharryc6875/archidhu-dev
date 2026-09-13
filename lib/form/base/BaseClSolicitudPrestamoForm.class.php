<?php

/**
 * ClSolicitudPrestamo form base class.
 *
 * @method ClSolicitudPrestamo getObject() Returns the current form's model object
 *
 * @package    ##PROJECT_NAME##
 * @subpackage form
 * @author     ##AUTHOR_NAME##
 */
abstract class BaseClSolicitudPrestamoForm extends BaseFormPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'CLSOLICITUDPRESTAMO_ID'       => new sfWidgetFormInputHidden(),
      'CLIENTE_ID'                   => new sfWidgetFormPropelChoice(array('model' => 'Cliente', 'add_empty' => false)),
      'USUARIO_ID'                   => new sfWidgetFormPropelChoice(array('model' => 'Usuario', 'add_empty' => false)),
      'CLSOLICITUDPRESTAMOESTADO_ID' => new sfWidgetFormPropelChoice(array('model' => 'ClSolicitudPrestamoEstado', 'add_empty' => false)),
      'FECHA_CREACION'               => new sfWidgetFormDateTime(),
      'FECHA_ATENCION'               => new sfWidgetFormDateTime(),
    ));

    $this->setValidators(array(
      'CLSOLICITUDPRESTAMO_ID'       => new sfValidatorChoice(array('choices' => array($this->getObject()->getClsolicitudprestamoId()), 'empty_value' => $this->getObject()->getClsolicitudprestamoId(), 'required' => false)),
      'CLIENTE_ID'                   => new sfValidatorPropelChoice(array('model' => 'Cliente', 'column' => 'CLIENTE_ID')),
      'USUARIO_ID'                   => new sfValidatorPropelChoice(array('model' => 'Usuario', 'column' => 'USUARIO_ID')),
      'CLSOLICITUDPRESTAMOESTADO_ID' => new sfValidatorPropelChoice(array('model' => 'ClSolicitudPrestamoEstado', 'column' => 'CLSOLICITUDPRESTAMOESTADO_ID')),
      'FECHA_CREACION'               => new sfValidatorDateTime(array('required' => false)),
      'FECHA_ATENCION'               => new sfValidatorDateTime(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('cl_solicitud_prestamo[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'ClSolicitudPrestamo';
  }


}
