<?php

/**
 * ClDetallePrestamo form base class.
 *
 * @method ClDetallePrestamo getObject() Returns the current form's model object
 *
 * @package    ##PROJECT_NAME##
 * @subpackage form
 * @author     ##AUTHOR_NAME##
 */
abstract class BaseClDetallePrestamoForm extends BaseFormPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'CLDETALLEPRESTAMO_ID'   => new sfWidgetFormInputHidden(),
      'CLPRESTAMO_ID'          => new sfWidgetFormPropelChoice(array('model' => 'ClPrestamo', 'add_empty' => false)),
      'CLSOLICITUDPRESTAMO_ID' => new sfWidgetFormPropelChoice(array('model' => 'ClSolicitudPrestamo', 'add_empty' => false)),
      'CLESTADOPRESTAMO_ID'    => new sfWidgetFormPropelChoice(array('model' => 'ClEstadoPrestamo', 'add_empty' => false)),
    ));

    $this->setValidators(array(
      'CLDETALLEPRESTAMO_ID'   => new sfValidatorChoice(array('choices' => array($this->getObject()->getCldetalleprestamoId()), 'empty_value' => $this->getObject()->getCldetalleprestamoId(), 'required' => false)),
      'CLPRESTAMO_ID'          => new sfValidatorPropelChoice(array('model' => 'ClPrestamo', 'column' => 'CLPRESTAMO_ID')),
      'CLSOLICITUDPRESTAMO_ID' => new sfValidatorPropelChoice(array('model' => 'ClSolicitudPrestamo', 'column' => 'CLSOLICITUDPRESTAMO_ID')),
      'CLESTADOPRESTAMO_ID'    => new sfValidatorPropelChoice(array('model' => 'ClEstadoPrestamo', 'column' => 'CLESTADOPRESTAMO_ID')),
    ));

    $this->widgetSchema->setNameFormat('cl_detalle_prestamo[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'ClDetallePrestamo';
  }


}
