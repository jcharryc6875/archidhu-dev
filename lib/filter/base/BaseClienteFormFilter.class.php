<?php

/**
 * Cliente filter form base class.
 *
 * @package    simad
 * @subpackage filter
 * @author     Your name here
 */
abstract class BaseClienteFormFilter extends BaseFormFilterPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'FRECCONSULTACLIENTE_ID' => new sfWidgetFormPropelChoice(array('model' => 'FrecConsultaCliente', 'add_empty' => true)),
      'SUBSERIE_ID'            => new sfWidgetFormFilterInput(),
      'CLIENTEESTADO_ID'       => new sfWidgetFormPropelChoice(array('model' => 'ClienteEstado', 'add_empty' => true)),
      'UNIDCONSERVADORA_ID'    => new sfWidgetFormPropelChoice(array('model' => 'Unidconservadora', 'add_empty' => true)),
      'SOPORTE_CLIENTE_ID'     => new sfWidgetFormPropelChoice(array('model' => 'SoporteCliente', 'add_empty' => true)),
      'CODIGO_CLIENTE'         => new sfWidgetFormFilterInput(),
      'FECHA_APERTURA'         => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate())),
      'FECHA_CIERRE'           => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate())),
      'NOMBRE_CLIENTE'         => new sfWidgetFormFilterInput(),
      'CONTENIDO'              => new sfWidgetFormFilterInput(),
      'CREADO_POR_WEB'         => new sfWidgetFormFilterInput(),
      'UBICACION'              => new sfWidgetFormFilterInput(),
      'FOLIOS'                 => new sfWidgetFormFilterInput(),
      'FECHA_CREACION'         => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate())),
      'NOTAS'                  => new sfWidgetFormFilterInput(),
      'FECHA_VENCIMIENTO'      => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate())),
      'VOLUMEN'                => new sfWidgetFormFilterInput(),
      'FECHA_AFILIACION'       => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate())),
      'MARCA'                  => new sfWidgetFormFilterInput(),
    ));

    $this->setValidators(array(
      'FRECCONSULTACLIENTE_ID' => new sfValidatorPropelChoice(array('required' => false, 'model' => 'FrecConsultaCliente', 'column' => 'FRECCONSULTACLIENTE_ID')),
      'SUBSERIE_ID'            => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'CLIENTEESTADO_ID'       => new sfValidatorPropelChoice(array('required' => false, 'model' => 'ClienteEstado', 'column' => 'CLIENTEESTADO_ID')),
      'UNIDCONSERVADORA_ID'    => new sfValidatorPropelChoice(array('required' => false, 'model' => 'Unidconservadora', 'column' => 'UNIDCONSERVADORA_ID')),
      'SOPORTE_CLIENTE_ID'     => new sfValidatorPropelChoice(array('required' => false, 'model' => 'SoporteCliente', 'column' => 'SOPORTE_CLIENTE_ID')),
      'CODIGO_CLIENTE'         => new sfValidatorPass(array('required' => false)),
      'FECHA_APERTURA'         => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDate(array('required' => false)), 'to_date' => new sfValidatorDate(array('required' => false)))),
      'FECHA_CIERRE'           => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDate(array('required' => false)), 'to_date' => new sfValidatorDate(array('required' => false)))),
      'NOMBRE_CLIENTE'         => new sfValidatorPass(array('required' => false)),
      'CONTENIDO'              => new sfValidatorPass(array('required' => false)),
      'CREADO_POR_WEB'         => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'UBICACION'              => new sfValidatorPass(array('required' => false)),
      'FOLIOS'                 => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'FECHA_CREACION'         => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDate(array('required' => false)), 'to_date' => new sfValidatorDate(array('required' => false)))),
      'NOTAS'                  => new sfValidatorPass(array('required' => false)),
      'FECHA_VENCIMIENTO'      => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDate(array('required' => false)), 'to_date' => new sfValidatorDate(array('required' => false)))),
      'VOLUMEN'                => new sfValidatorSchemaFilter('text', new sfValidatorNumber(array('required' => false))),
      'FECHA_AFILIACION'       => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDate(array('required' => false)), 'to_date' => new sfValidatorDate(array('required' => false)))),
      'MARCA'                  => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
    ));

    $this->widgetSchema->setNameFormat('cliente_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'Cliente';
  }

  public function getFields()
  {
    return array(
      'CLIENTE_ID'             => 'Number',
      'FRECCONSULTACLIENTE_ID' => 'ForeignKey',
      'SUBSERIE_ID'            => 'Number',
      'CLIENTEESTADO_ID'       => 'ForeignKey',
      'UNIDCONSERVADORA_ID'    => 'ForeignKey',
      'SOPORTE_CLIENTE_ID'     => 'ForeignKey',
      'CODIGO_CLIENTE'         => 'Text',
      'FECHA_APERTURA'         => 'Date',
      'FECHA_CIERRE'           => 'Date',
      'NOMBRE_CLIENTE'         => 'Text',
      'CONTENIDO'              => 'Text',
      'CREADO_POR_WEB'         => 'Number',
      'UBICACION'              => 'Text',
      'FOLIOS'                 => 'Number',
      'FECHA_CREACION'         => 'Date',
      'NOTAS'                  => 'Text',
      'FECHA_VENCIMIENTO'      => 'Date',
      'VOLUMEN'                => 'Number',
      'FECHA_AFILIACION'       => 'Date',
      'MARCA'                  => 'Number',
    );
  }
}
