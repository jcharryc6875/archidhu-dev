<?php

/**
 * NotaPqr filter form base class.
 *
 * @package    simad
 * @subpackage filter
 * @author     Your name here
 */
abstract class BaseNotaPqrFormFilter extends BaseFormFilterPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'PQR_ID'     => new sfWidgetFormPropelChoice(array('model' => 'Pqr', 'add_empty' => true)),
      'USUARIO_ID' => new sfWidgetFormPropelChoice(array('model' => 'Usuario', 'add_empty' => true)),
      'CONTENIDO'  => new sfWidgetFormFilterInput(),
      'RUTA'       => new sfWidgetFormFilterInput(),
      'FECHA'      => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate())),
    ));

    $this->setValidators(array(
      'PQR_ID'     => new sfValidatorPropelChoice(array('required' => false, 'model' => 'Pqr', 'column' => 'PQR_ID')),
      'USUARIO_ID' => new sfValidatorPropelChoice(array('required' => false, 'model' => 'Usuario', 'column' => 'USUARIO_ID')),
      'CONTENIDO'  => new sfValidatorPass(array('required' => false)),
      'RUTA'       => new sfValidatorPass(array('required' => false)),
      'FECHA'      => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDate(array('required' => false)), 'to_date' => new sfValidatorDate(array('required' => false)))),
    ));

    $this->widgetSchema->setNameFormat('nota_pqr_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'NotaPqr';
  }

  public function getFields()
  {
    return array(
      'NOTAPQR_ID' => 'Number',
      'PQR_ID'     => 'ForeignKey',
      'USUARIO_ID' => 'ForeignKey',
      'CONTENIDO'  => 'Text',
      'RUTA'       => 'Text',
      'FECHA'      => 'Date',
    );
  }
}
