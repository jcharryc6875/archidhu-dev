<?php

/**
 * DetallePrestamo form base class.
 *
 * @method DetallePrestamo getObject() Returns the current form's model object
 *
 * @package    ##PROJECT_NAME##
 * @subpackage form
 * @author     ##AUTHOR_NAME##
 */
abstract class BaseDetallePrestamoForm extends BaseFormPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'DETALLEPRESTAMO_ID'   => new sfWidgetFormInputHidden(),
      'PRESTAMO_ID'          => new sfWidgetFormPropelChoice(array('model' => 'Prestamo', 'add_empty' => false)),
      'ESTADOPRESTAMO_ID'    => new sfWidgetFormPropelChoice(array('model' => 'EstadoPrestamo', 'add_empty' => false)),
      'SOLICITUDPRESTAMO_ID' => new sfWidgetFormPropelChoice(array('model' => 'SolicitudPrestamo', 'add_empty' => false)),
    ));

    $this->setValidators(array(
      'DETALLEPRESTAMO_ID'   => new sfValidatorChoice(array('choices' => array($this->getObject()->getDetalleprestamoId()), 'empty_value' => $this->getObject()->getDetalleprestamoId(), 'required' => false)),
      'PRESTAMO_ID'          => new sfValidatorPropelChoice(array('model' => 'Prestamo', 'column' => 'PRESTAMO_ID')),
      'ESTADOPRESTAMO_ID'    => new sfValidatorPropelChoice(array('model' => 'EstadoPrestamo', 'column' => 'ESTADOPRESTAMO_ID')),
      'SOLICITUDPRESTAMO_ID' => new sfValidatorPropelChoice(array('model' => 'SolicitudPrestamo', 'column' => 'SOLICITUDPRESTAMO_ID')),
    ));

    $this->widgetSchema->setNameFormat('detalle_prestamo[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'DetallePrestamo';
  }


}
