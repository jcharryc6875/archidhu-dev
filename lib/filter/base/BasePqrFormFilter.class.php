<?php

/**
 * Pqr filter form base class.
 *
 * @package    simad
 * @subpackage filter
 * @author     Your name here
 */
abstract class BasePqrFormFilter extends BaseFormFilterPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'PRIORIDADPQR_ID' => new sfWidgetFormPropelChoice(array('model' => 'PrioridadPqr', 'add_empty' => true)),
      'CATEGORIAPQR_ID' => new sfWidgetFormPropelChoice(array('model' => 'CategoriaPqr', 'add_empty' => true)),
      'USUARIO_ID'      => new sfWidgetFormPropelChoice(array('model' => 'Usuario', 'add_empty' => true)),
      'ESTADOPQR_ID'    => new sfWidgetFormPropelChoice(array('model' => 'EstadoPqr', 'add_empty' => true)),
      'OBJETO'          => new sfWidgetFormFilterInput(),
      'CONTENIDO'       => new sfWidgetFormFilterInput(),
      'RUTA'            => new sfWidgetFormFilterInput(),
      'FECHA_CREACION'  => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate())),
    ));

    $this->setValidators(array(
      'PRIORIDADPQR_ID' => new sfValidatorPropelChoice(array('required' => false, 'model' => 'PrioridadPqr', 'column' => 'PRIORIDADPQR_ID')),
      'CATEGORIAPQR_ID' => new sfValidatorPropelChoice(array('required' => false, 'model' => 'CategoriaPqr', 'column' => 'CATEGORIAPQR_ID')),
      'USUARIO_ID'      => new sfValidatorPropelChoice(array('required' => false, 'model' => 'Usuario', 'column' => 'USUARIO_ID')),
      'ESTADOPQR_ID'    => new sfValidatorPropelChoice(array('required' => false, 'model' => 'EstadoPqr', 'column' => 'ESTADOPQR_ID')),
      'OBJETO'          => new sfValidatorPass(array('required' => false)),
      'CONTENIDO'       => new sfValidatorPass(array('required' => false)),
      'RUTA'            => new sfValidatorPass(array('required' => false)),
      'FECHA_CREACION'  => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDate(array('required' => false)), 'to_date' => new sfValidatorDate(array('required' => false)))),
    ));

    $this->widgetSchema->setNameFormat('pqr_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'Pqr';
  }

  public function getFields()
  {
    return array(
      'PQR_ID'          => 'Number',
      'PRIORIDADPQR_ID' => 'ForeignKey',
      'CATEGORIAPQR_ID' => 'ForeignKey',
      'USUARIO_ID'      => 'ForeignKey',
      'ESTADOPQR_ID'    => 'ForeignKey',
      'OBJETO'          => 'Text',
      'CONTENIDO'       => 'Text',
      'RUTA'            => 'Text',
      'FECHA_CREACION'  => 'Date',
    );
  }
}
