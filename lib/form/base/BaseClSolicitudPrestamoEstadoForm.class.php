<?php

/**
 * ClSolicitudPrestamoEstado form base class.
 *
 * @method ClSolicitudPrestamoEstado getObject() Returns the current form's model object
 *
 * @package    ##PROJECT_NAME##
 * @subpackage form
 * @author     ##AUTHOR_NAME##
 */
abstract class BaseClSolicitudPrestamoEstadoForm extends BaseFormPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'CLSOLICITUDPRESTAMOESTADO_ID' => new sfWidgetFormInputHidden(),
      'DESCRIPCION'                  => new sfWidgetFormInputText(),
    ));

    $this->setValidators(array(
      'CLSOLICITUDPRESTAMOESTADO_ID' => new sfValidatorChoice(array('choices' => array($this->getObject()->getClsolicitudprestamoestadoId()), 'empty_value' => $this->getObject()->getClsolicitudprestamoestadoId(), 'required' => false)),
      'DESCRIPCION'                  => new sfValidatorString(array('max_length' => 250, 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('cl_solicitud_prestamo_estado[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'ClSolicitudPrestamoEstado';
  }


}
