<?php

/**
 * SolicitudPrestamoEstado form base class.
 *
 * @method SolicitudPrestamoEstado getObject() Returns the current form's model object
 *
 * @package    ##PROJECT_NAME##
 * @subpackage form
 * @author     ##AUTHOR_NAME##
 */
abstract class BaseSolicitudPrestamoEstadoForm extends BaseFormPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'SOLICITUDPRESTAMOESTADO_ID' => new sfWidgetFormInputHidden(),
      'DESCRIPCION'                => new sfWidgetFormInputText(),
    ));

    $this->setValidators(array(
      'SOLICITUDPRESTAMOESTADO_ID' => new sfValidatorChoice(array('choices' => array($this->getObject()->getSolicitudprestamoestadoId()), 'empty_value' => $this->getObject()->getSolicitudprestamoestadoId(), 'required' => false)),
      'DESCRIPCION'                => new sfValidatorString(array('max_length' => 250, 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('solicitud_prestamo_estado[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'SolicitudPrestamoEstado';
  }


}
