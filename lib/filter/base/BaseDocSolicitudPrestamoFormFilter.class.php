<?php

/**
 * DocSolicitudPrestamo filter form base class.
 *
 * @package    simad
 * @subpackage filter
 * @author     Your name here
 */
abstract class BaseDocSolicitudPrestamoFormFilter extends BaseFormFilterPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'DOCUMENTACION_ID'              => new sfWidgetFormPropelChoice(array('model' => 'Documentacion', 'add_empty' => true)),
      'USUARIO_ID'                    => new sfWidgetFormPropelChoice(array('model' => 'Usuario', 'add_empty' => true)),
      'DOCSOLICITUDPRESTAMOESTADO_ID' => new sfWidgetFormPropelChoice(array('model' => 'DocSolicitudPrestamoEstado', 'add_empty' => true)),
      'FECHA_CREACION'                => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate())),
      'FECHA_ATENCION'                => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate())),
    ));

    $this->setValidators(array(
      'DOCUMENTACION_ID'              => new sfValidatorPropelChoice(array('required' => false, 'model' => 'Documentacion', 'column' => 'DOCUMENTACION_ID')),
      'USUARIO_ID'                    => new sfValidatorPropelChoice(array('required' => false, 'model' => 'Usuario', 'column' => 'USUARIO_ID')),
      'DOCSOLICITUDPRESTAMOESTADO_ID' => new sfValidatorPropelChoice(array('required' => false, 'model' => 'DocSolicitudPrestamoEstado', 'column' => 'DOCSOLICITUDPRESTAMOESTADO_ID')),
      'FECHA_CREACION'                => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDate(array('required' => false)), 'to_date' => new sfValidatorDate(array('required' => false)))),
      'FECHA_ATENCION'                => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDate(array('required' => false)), 'to_date' => new sfValidatorDate(array('required' => false)))),
    ));

    $this->widgetSchema->setNameFormat('doc_solicitud_prestamo_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'DocSolicitudPrestamo';
  }

  public function getFields()
  {
    return array(
      'DOCSOLICITUDPRESTAMO_ID'       => 'Number',
      'DOCUMENTACION_ID'              => 'ForeignKey',
      'USUARIO_ID'                    => 'ForeignKey',
      'DOCSOLICITUDPRESTAMOESTADO_ID' => 'ForeignKey',
      'FECHA_CREACION'                => 'Date',
      'FECHA_ATENCION'                => 'Date',
    );
  }
}
