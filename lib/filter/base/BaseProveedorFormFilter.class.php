<?php

/**
 * Proveedor filter form base class.
 *
 * @package    simad
 * @subpackage filter
 * @author     Your name here
 */
abstract class BaseProveedorFormFilter extends BaseFormFilterPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'NOMBRE'              => new sfWidgetFormFilterInput(),
      'NIT'                 => new sfWidgetFormFilterInput(),
      'REPRESENTANTE_LEGAL' => new sfWidgetFormFilterInput(),
      'IDENTIFICACION_RL'   => new sfWidgetFormFilterInput(),
      'DIRECCION'           => new sfWidgetFormFilterInput(),
      'TELEFONO'            => new sfWidgetFormFilterInput(),
      'NATURALEZA_JURIDICA' => new sfWidgetFormFilterInput(),
      'PAIS_ID'             => new sfWidgetFormPropelChoice(array('model' => 'Pais', 'add_empty' => true)),
      'PROV_ESTADO_ID'      => new sfWidgetFormPropelChoice(array('model' => 'ProvEstado', 'add_empty' => true)),
      'NOMBRE_COMERCIAL'    => new sfWidgetFormFilterInput(),
      'FECHA_INGRESO'       => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate())),
      'FECHA_CREACION'      => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate())),
    ));

    $this->setValidators(array(
      'NOMBRE'              => new sfValidatorPass(array('required' => false)),
      'NIT'                 => new sfValidatorPass(array('required' => false)),
      'REPRESENTANTE_LEGAL' => new sfValidatorPass(array('required' => false)),
      'IDENTIFICACION_RL'   => new sfValidatorPass(array('required' => false)),
      'DIRECCION'           => new sfValidatorPass(array('required' => false)),
      'TELEFONO'            => new sfValidatorPass(array('required' => false)),
      'NATURALEZA_JURIDICA' => new sfValidatorPass(array('required' => false)),
      'PAIS_ID'             => new sfValidatorPropelChoice(array('required' => false, 'model' => 'Pais', 'column' => 'PAIS_ID')),
      'PROV_ESTADO_ID'      => new sfValidatorPropelChoice(array('required' => false, 'model' => 'ProvEstado', 'column' => 'PROV_ESTADO_ID')),
      'NOMBRE_COMERCIAL'    => new sfValidatorPass(array('required' => false)),
      'FECHA_INGRESO'       => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDate(array('required' => false)), 'to_date' => new sfValidatorDate(array('required' => false)))),
      'FECHA_CREACION'      => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDate(array('required' => false)), 'to_date' => new sfValidatorDate(array('required' => false)))),
    ));

    $this->widgetSchema->setNameFormat('proveedor_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'Proveedor';
  }

  public function getFields()
  {
    return array(
      'PROVEEDOR_ID'        => 'Number',
      'NOMBRE'              => 'Text',
      'NIT'                 => 'Text',
      'REPRESENTANTE_LEGAL' => 'Text',
      'IDENTIFICACION_RL'   => 'Text',
      'DIRECCION'           => 'Text',
      'TELEFONO'            => 'Text',
      'NATURALEZA_JURIDICA' => 'Text',
      'PAIS_ID'             => 'ForeignKey',
      'PROV_ESTADO_ID'      => 'ForeignKey',
      'NOMBRE_COMERCIAL'    => 'Text',
      'FECHA_INGRESO'       => 'Date',
      'FECHA_CREACION'      => 'Date',
    );
  }
}
