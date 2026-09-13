<?php

/**
 * DocDetallePrestamo filter form base class.
 *
 * @package    simad
 * @subpackage filter
 * @author     Your name here
 */
abstract class BaseDocDetallePrestamoFormFilter extends BaseFormFilterPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'DOCPRESTAMO_ID'          => new sfWidgetFormPropelChoice(array('model' => 'DocPrestamo', 'add_empty' => true)),
      'DOCSOLICITUDPRESTAMO_ID' => new sfWidgetFormPropelChoice(array('model' => 'DocSolicitudPrestamo', 'add_empty' => true)),
      'DOCESTADOPRESTAMO_ID'    => new sfWidgetFormPropelChoice(array('model' => 'DocEstadoPrestamo', 'add_empty' => true)),
    ));

    $this->setValidators(array(
      'DOCPRESTAMO_ID'          => new sfValidatorPropelChoice(array('required' => false, 'model' => 'DocPrestamo', 'column' => 'DOCPRESTAMO_ID')),
      'DOCSOLICITUDPRESTAMO_ID' => new sfValidatorPropelChoice(array('required' => false, 'model' => 'DocSolicitudPrestamo', 'column' => 'DOCSOLICITUDPRESTAMO_ID')),
      'DOCESTADOPRESTAMO_ID'    => new sfValidatorPropelChoice(array('required' => false, 'model' => 'DocEstadoPrestamo', 'column' => 'DOCESTADOPRESTAMO_ID')),
    ));

    $this->widgetSchema->setNameFormat('doc_detalle_prestamo_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'DocDetallePrestamo';
  }

  public function getFields()
  {
    return array(
      'DOCDETALLEPRESTAMO_ID'   => 'Number',
      'DOCPRESTAMO_ID'          => 'ForeignKey',
      'DOCSOLICITUDPRESTAMO_ID' => 'ForeignKey',
      'DOCESTADOPRESTAMO_ID'    => 'ForeignKey',
    );
  }
}
