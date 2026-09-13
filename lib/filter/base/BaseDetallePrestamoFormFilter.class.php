<?php

/**
 * DetallePrestamo filter form base class.
 *
 * @package    simad
 * @subpackage filter
 * @author     Your name here
 */
abstract class BaseDetallePrestamoFormFilter extends BaseFormFilterPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'PRESTAMO_ID'          => new sfWidgetFormPropelChoice(array('model' => 'Prestamo', 'add_empty' => true)),
      'ESTADOPRESTAMO_ID'    => new sfWidgetFormPropelChoice(array('model' => 'EstadoPrestamo', 'add_empty' => true)),
      'SOLICITUDPRESTAMO_ID' => new sfWidgetFormPropelChoice(array('model' => 'SolicitudPrestamo', 'add_empty' => true)),
    ));

    $this->setValidators(array(
      'PRESTAMO_ID'          => new sfValidatorPropelChoice(array('required' => false, 'model' => 'Prestamo', 'column' => 'PRESTAMO_ID')),
      'ESTADOPRESTAMO_ID'    => new sfValidatorPropelChoice(array('required' => false, 'model' => 'EstadoPrestamo', 'column' => 'ESTADOPRESTAMO_ID')),
      'SOLICITUDPRESTAMO_ID' => new sfValidatorPropelChoice(array('required' => false, 'model' => 'SolicitudPrestamo', 'column' => 'SOLICITUDPRESTAMO_ID')),
    ));

    $this->widgetSchema->setNameFormat('detalle_prestamo_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'DetallePrestamo';
  }

  public function getFields()
  {
    return array(
      'DETALLEPRESTAMO_ID'   => 'Number',
      'PRESTAMO_ID'          => 'ForeignKey',
      'ESTADOPRESTAMO_ID'    => 'ForeignKey',
      'SOLICITUDPRESTAMO_ID' => 'ForeignKey',
    );
  }
}
