<?php

/**
 * Novedades filter form base class.
 *
 * @package    simad
 * @subpackage filter
 * @author     Your name here
 */
abstract class BaseNovedadesFormFilter extends BaseFormFilterPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'USUARIO_ID'       => new sfWidgetFormPropelChoice(array('model' => 'Usuario', 'add_empty' => true)),
      'MODULO_ID'        => new sfWidgetFormPropelChoice(array('model' => 'Modulo', 'add_empty' => true)),
      'TIPOCONTROL_ID'   => new sfWidgetFormPropelChoice(array('model' => 'TipoControl', 'add_empty' => true)),
      'CODIGO_PRINCIPAL' => new sfWidgetFormFilterInput(),
      'DESCRIPCION'      => new sfWidgetFormFilterInput(),
      'FECHA_CREACION'   => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate())),
      'RUTA'             => new sfWidgetFormFilterInput(),
      'REMITENTE'        => new sfWidgetFormFilterInput(),
      'DESTINATARIO'     => new sfWidgetFormFilterInput(),
      'VALOR_FACTURA'    => new sfWidgetFormFilterInput(),
      'TIPO_DOCUMENTO'   => new sfWidgetFormFilterInput(),
      'GUIA'             => new sfWidgetFormFilterInput(),
      'NUMERO_FACTURA'   => new sfWidgetFormFilterInput(),
    ));

    $this->setValidators(array(
      'USUARIO_ID'       => new sfValidatorPropelChoice(array('required' => false, 'model' => 'Usuario', 'column' => 'USUARIO_ID')),
      'MODULO_ID'        => new sfValidatorPropelChoice(array('required' => false, 'model' => 'Modulo', 'column' => 'MODULO_ID')),
      'TIPOCONTROL_ID'   => new sfValidatorPropelChoice(array('required' => false, 'model' => 'TipoControl', 'column' => 'TIPOCONTROL_ID')),
      'CODIGO_PRINCIPAL' => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'DESCRIPCION'      => new sfValidatorPass(array('required' => false)),
      'FECHA_CREACION'   => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDate(array('required' => false)), 'to_date' => new sfValidatorDate(array('required' => false)))),
      'RUTA'             => new sfValidatorPass(array('required' => false)),
      'REMITENTE'        => new sfValidatorPass(array('required' => false)),
      'DESTINATARIO'     => new sfValidatorPass(array('required' => false)),
      'VALOR_FACTURA'    => new sfValidatorPass(array('required' => false)),
      'TIPO_DOCUMENTO'   => new sfValidatorPass(array('required' => false)),
      'GUIA'             => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'NUMERO_FACTURA'   => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
    ));

    $this->widgetSchema->setNameFormat('novedades_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'Novedades';
  }

  public function getFields()
  {
    return array(
      'NOVEDADES_ID'     => 'Number',
      'USUARIO_ID'       => 'ForeignKey',
      'MODULO_ID'        => 'ForeignKey',
      'TIPOCONTROL_ID'   => 'ForeignKey',
      'CODIGO_PRINCIPAL' => 'Number',
      'DESCRIPCION'      => 'Text',
      'FECHA_CREACION'   => 'Date',
      'RUTA'             => 'Text',
      'REMITENTE'        => 'Text',
      'DESTINATARIO'     => 'Text',
      'VALOR_FACTURA'    => 'Text',
      'TIPO_DOCUMENTO'   => 'Text',
      'GUIA'             => 'Number',
      'NUMERO_FACTURA'   => 'Number',
    );
  }
}
