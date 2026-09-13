<?php

/**
 * PrioridadSolicitudServicio form base class.
 *
 * @method PrioridadSolicitudServicio getObject() Returns the current form's model object
 *
 * @package    ##PROJECT_NAME##
 * @subpackage form
 * @author     ##AUTHOR_NAME##
 */
abstract class BasePrioridadSolicitudServicioForm extends BaseFormPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'PRIORIDADSOLICITUDSERVICIO_ID' => new sfWidgetFormInputHidden(),
      'DESCRIPCION'                   => new sfWidgetFormInputText(),
      'ES_VISIBLE'                    => new sfWidgetFormInputText(),
    ));

    $this->setValidators(array(
      'PRIORIDADSOLICITUDSERVICIO_ID' => new sfValidatorChoice(array('choices' => array($this->getObject()->getPrioridadsolicitudservicioId()), 'empty_value' => $this->getObject()->getPrioridadsolicitudservicioId(), 'required' => false)),
      'DESCRIPCION'                   => new sfValidatorString(array('max_length' => 250, 'required' => false)),
      'ES_VISIBLE'                    => new sfValidatorInteger(array('min' => -2147483648, 'max' => 2147483647, 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('prioridad_solicitud_servicio[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'PrioridadSolicitudServicio';
  }


}
