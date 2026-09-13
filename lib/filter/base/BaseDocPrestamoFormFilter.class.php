<?php

/**
 * DocPrestamo filter form base class.
 *
 * @package    simad
 * @subpackage filter
 * @author     Your name here
 */
abstract class BaseDocPrestamoFormFilter extends BaseFormFilterPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'REGIONAL_ID'          => new sfWidgetFormPropelChoice(array('model' => 'Regional', 'add_empty' => true)),
      'PERIODO_ID'           => new sfWidgetFormPropelChoice(array('model' => 'Periodo', 'add_empty' => true)),
      'USUARIO_ID'           => new sfWidgetFormPropelChoice(array('model' => 'Usuario', 'add_empty' => true)),
      'DOCESTADOPRESTAMO_ID' => new sfWidgetFormPropelChoice(array('model' => 'DocEstadoPrestamo', 'add_empty' => true)),
      'FECHA_PRESTAMO'       => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate())),
      'FECHA_VENCIMIENTO'    => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate())),
      'FECHA_DEVOLUCION'     => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate())),
      'OBSERVACIONES'        => new sfWidgetFormFilterInput(),
      'CONSECUTIVO_REGIONAL' => new sfWidgetFormFilterInput(),
      'NUMERO_RADICACION'    => new sfWidgetFormFilterInput(),
    ));

    $this->setValidators(array(
      'REGIONAL_ID'          => new sfValidatorPropelChoice(array('required' => false, 'model' => 'Regional', 'column' => 'REGIONAL_ID')),
      'PERIODO_ID'           => new sfValidatorPropelChoice(array('required' => false, 'model' => 'Periodo', 'column' => 'PERIODO_ID')),
      'USUARIO_ID'           => new sfValidatorPropelChoice(array('required' => false, 'model' => 'Usuario', 'column' => 'USUARIO_ID')),
      'DOCESTADOPRESTAMO_ID' => new sfValidatorPropelChoice(array('required' => false, 'model' => 'DocEstadoPrestamo', 'column' => 'DOCESTADOPRESTAMO_ID')),
      'FECHA_PRESTAMO'       => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDate(array('required' => false)), 'to_date' => new sfValidatorDate(array('required' => false)))),
      'FECHA_VENCIMIENTO'    => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDate(array('required' => false)), 'to_date' => new sfValidatorDate(array('required' => false)))),
      'FECHA_DEVOLUCION'     => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDate(array('required' => false)), 'to_date' => new sfValidatorDate(array('required' => false)))),
      'OBSERVACIONES'        => new sfValidatorPass(array('required' => false)),
      'CONSECUTIVO_REGIONAL' => new sfValidatorPass(array('required' => false)),
      'NUMERO_RADICACION'    => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
    ));

    $this->widgetSchema->setNameFormat('doc_prestamo_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'DocPrestamo';
  }

  public function getFields()
  {
    return array(
      'DOCPRESTAMO_ID'       => 'Number',
      'REGIONAL_ID'          => 'ForeignKey',
      'PERIODO_ID'           => 'ForeignKey',
      'USUARIO_ID'           => 'ForeignKey',
      'DOCESTADOPRESTAMO_ID' => 'ForeignKey',
      'FECHA_PRESTAMO'       => 'Date',
      'FECHA_VENCIMIENTO'    => 'Date',
      'FECHA_DEVOLUCION'     => 'Date',
      'OBSERVACIONES'        => 'Text',
      'CONSECUTIVO_REGIONAL' => 'Text',
      'NUMERO_RADICACION'    => 'Number',
    );
  }
}
