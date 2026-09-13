<?php

/**
 * CargoUsuario filter form base class.
 *
 * @package    simad
 * @subpackage filter
 * @author     Your name here
 */
abstract class BaseCargoUsuarioFormFilter extends BaseFormFilterPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'CARGO_ID'        => new sfWidgetFormPropelChoice(array('model' => 'Cargo', 'add_empty' => true)),
      'USUARIO_ID'      => new sfWidgetFormPropelChoice(array('model' => 'Usuario', 'add_empty' => true)),
      'FECHA_INICIO'    => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate())),
      'FECHA_FIN'       => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate())),
      'FECHA_CREACION'  => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate())),
      'ES_ACTUAL'       => new sfWidgetFormFilterInput(),
      'ES_PRINCIPAL'    => new sfWidgetFormFilterInput(),
    ));

    $this->setValidators(array(
      'CARGO_ID'        => new sfValidatorPropelChoice(array('required' => false, 'model' => 'Cargo', 'column' => 'CARGO_ID')),
      'USUARIO_ID'      => new sfValidatorPropelChoice(array('required' => false, 'model' => 'Usuario', 'column' => 'USUARIO_ID')),
      'FECHA_INICIO'    => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDate(array('required' => false)), 'to_date' => new sfValidatorDate(array('required' => false)))),
      'FECHA_FIN'       => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDate(array('required' => false)), 'to_date' => new sfValidatorDate(array('required' => false)))),
      'FECHA_CREACION'  => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDate(array('required' => false)), 'to_date' => new sfValidatorDate(array('required' => false)))),
      'ES_ACTUAL'       => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'ES_PRINCIPAL'    => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
    ));

    $this->widgetSchema->setNameFormat('cargo_usuario_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'CargoUsuario';
  }

  public function getFields()
  {
    return array(
      'CARGOUSUARIO_ID' => 'Number',
      'CARGO_ID'        => 'ForeignKey',
      'USUARIO_ID'      => 'ForeignKey',
      'FECHA_INICIO'    => 'Date',
      'FECHA_FIN'       => 'Date',
      'FECHA_CREACION'  => 'Date',
      'ES_ACTUAL'       => 'Number',
      'ES_PRINCIPAL'    => 'Number',
    );
  }
}
