<?php

/**
 * SolicitudPrestamo form base class.
 *
 * @method SolicitudPrestamo getObject() Returns the current form's model object
 *
 * @package    ##PROJECT_NAME##
 * @subpackage form
 * @author     ##AUTHOR_NAME##
 */
abstract class BaseSolicitudPrestamoForm extends BaseFormPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'SOLICITUDPRESTAMO_ID'         => new sfWidgetFormInputHidden(),
      'USUARIO_ID'                   => new sfWidgetFormPropelChoice(array('model' => 'Usuario', 'add_empty' => false)),
      'SOLICITUDPRESTAMOESTADO_ID'   => new sfWidgetFormPropelChoice(array('model' => 'SolicitudPrestamoEstado', 'add_empty' => false)),
      'UNIDADDOCUMENTAL_ID'          => new sfWidgetFormPropelChoice(array('model' => 'UnidadDocumental', 'add_empty' => false)),
      'CONTENIDOUNIDADDOCUMENTAL_ID' => new sfWidgetFormPropelChoice(array('model' => 'ContenidoUnidadDocumental', 'add_empty' => true)),
      'FECHA_CREACION'               => new sfWidgetFormDateTime(),
      'FECHA_ATENCION'               => new sfWidgetFormDateTime(),
      'OBSERVACIONES'                => new sfWidgetFormInputText(),
      'MARCA'                        => new sfWidgetFormInputText(),
    ));

    $this->setValidators(array(
      'SOLICITUDPRESTAMO_ID'         => new sfValidatorChoice(array('choices' => array($this->getObject()->getSolicitudprestamoId()), 'empty_value' => $this->getObject()->getSolicitudprestamoId(), 'required' => false)),
      'USUARIO_ID'                   => new sfValidatorPropelChoice(array('model' => 'Usuario', 'column' => 'USUARIO_ID')),
      'SOLICITUDPRESTAMOESTADO_ID'   => new sfValidatorPropelChoice(array('model' => 'SolicitudPrestamoEstado', 'column' => 'SOLICITUDPRESTAMOESTADO_ID')),
      'UNIDADDOCUMENTAL_ID'          => new sfValidatorPropelChoice(array('model' => 'UnidadDocumental', 'column' => 'UNIDADDOCUMENTAL_ID')),
      'CONTENIDOUNIDADDOCUMENTAL_ID' => new sfValidatorPropelChoice(array('model' => 'ContenidoUnidadDocumental', 'column' => 'CONTENIDOUNIDADDOCUMENTAL_ID', 'required' => false)),
      'FECHA_CREACION'               => new sfValidatorDateTime(array('required' => false)),
      'FECHA_ATENCION'               => new sfValidatorDateTime(array('required' => false)),
      'OBSERVACIONES'                => new sfValidatorString(array('max_length' => 200, 'required' => false)),
      'MARCA'                        => new sfValidatorInteger(array('min' => -2147483648, 'max' => 2147483647, 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('solicitud_prestamo[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'SolicitudPrestamo';
  }


}
