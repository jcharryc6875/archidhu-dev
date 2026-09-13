<?php

/**
 * ClDetallePrestamo filter form base class.
 *
 * @package    simad
 * @subpackage filter
 * @author     Your name here
 */
abstract class BaseClDetallePrestamoFormFilter extends BaseFormFilterPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'CLPRESTAMO_ID'          => new sfWidgetFormPropelChoice(array('model' => 'ClPrestamo', 'add_empty' => true)),
      'CLSOLICITUDPRESTAMO_ID' => new sfWidgetFormPropelChoice(array('model' => 'ClSolicitudPrestamo', 'add_empty' => true)),
      'CLESTADOPRESTAMO_ID'    => new sfWidgetFormPropelChoice(array('model' => 'ClEstadoPrestamo', 'add_empty' => true)),
    ));

    $this->setValidators(array(
      'CLPRESTAMO_ID'          => new sfValidatorPropelChoice(array('required' => false, 'model' => 'ClPrestamo', 'column' => 'CLPRESTAMO_ID')),
      'CLSOLICITUDPRESTAMO_ID' => new sfValidatorPropelChoice(array('required' => false, 'model' => 'ClSolicitudPrestamo', 'column' => 'CLSOLICITUDPRESTAMO_ID')),
      'CLESTADOPRESTAMO_ID'    => new sfValidatorPropelChoice(array('required' => false, 'model' => 'ClEstadoPrestamo', 'column' => 'CLESTADOPRESTAMO_ID')),
    ));

    $this->widgetSchema->setNameFormat('cl_detalle_prestamo_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'ClDetallePrestamo';
  }

  public function getFields()
  {
    return array(
      'CLDETALLEPRESTAMO_ID'   => 'Number',
      'CLPRESTAMO_ID'          => 'ForeignKey',
      'CLSOLICITUDPRESTAMO_ID' => 'ForeignKey',
      'CLESTADOPRESTAMO_ID'    => 'ForeignKey',
    );
  }
}
