<?php

/**
 * ProvPeriodoValidez filter form base class.
 *
 * @package    simad
 * @subpackage filter
 * @author     Your name here
 */
abstract class BaseProvPeriodoValidezFormFilter extends BaseFormFilterPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'PROV_CORRESPONDIENTE_AUTOFACTURACION_ID' => new sfWidgetFormPropelChoice(array('model' => 'ProvCorrespondienteAutofacturacion', 'add_empty' => true)),
      'PROVEEDOR_ID'                            => new sfWidgetFormPropelChoice(array('model' => 'Proveedor', 'add_empty' => true)),
      'PROV_TIPO_INDUSTRIA_ID'                  => new sfWidgetFormPropelChoice(array('model' => 'ProvTipoIndustria', 'add_empty' => true)),
      'PROV_CONDICION_EXPEDICION_ID'            => new sfWidgetFormPropelChoice(array('model' => 'ProvCondicionExpedicion', 'add_empty' => true)),
      'PROV_RECHAZADA_CREACION2_ID'             => new sfWidgetFormPropelChoice(array('model' => 'ProvRechazadaCreacion2', 'add_empty' => true)),
      'PROV_APROBACION_ID'                      => new sfWidgetFormPropelChoice(array('model' => 'ProvAprobacion', 'add_empty' => true)),
      'PROV_REVISADO_CREACION_ID'               => new sfWidgetFormPropelChoice(array('model' => 'ProvRevisadoCreacion', 'add_empty' => true)),
      'PROV_CONDICION_PAGO_ID'                  => new sfWidgetFormPropelChoice(array('model' => 'ProvCondicionPago', 'add_empty' => true)),
      'PROV_GRUPO_TESORERIA_ID'                 => new sfWidgetFormPropelChoice(array('model' => 'ProvGrupoTesoreria', 'add_empty' => true)),
      'PRO_PROV_APROBACION_ID'                  => new sfWidgetFormPropelChoice(array('model' => 'ProvAprobacion', 'add_empty' => true)),
      'PROV_ADUANA_ENTRADA_ID'                  => new sfWidgetFormPropelChoice(array('model' => 'ProvAduanaEntrada', 'add_empty' => true)),
      'PROV_RECHAZADA_CREACION_ID'              => new sfWidgetFormPropelChoice(array('model' => 'ProvRechazadaCreacion', 'add_empty' => true)),
      'PROB_APROBADO_PARA_CREACION_ID'          => new sfWidgetFormPropelChoice(array('model' => 'ProbAprobadoParaCreacion', 'add_empty' => true)),
      'PROV_GRUPO_ESQUEMA_ID'                   => new sfWidgetFormPropelChoice(array('model' => 'ProvGrupoEsquema', 'add_empty' => true)),
      'PROV_MONEDA_PEDIDO_ID'                   => new sfWidgetFormPropelChoice(array('model' => 'ProvMonedaPedido', 'add_empty' => true)),
      'PROV_TIPO_CONTRIBUYENTE_ID'              => new sfWidgetFormPropelChoice(array('model' => 'ProvTipoContribuyente', 'add_empty' => true)),
      'PROV_CUENTA_ASOCIADA_ID'                 => new sfWidgetFormPropelChoice(array('model' => 'ProvCuentaAsociada', 'add_empty' => true)),
      'FECHA_INICIAL'                           => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate())),
      'FECHA_FINAL'                             => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate())),
      'OBSERVACIONES_GENERALES'                 => new sfWidgetFormFilterInput(),
      'FECHA_CREACION_SAP'                      => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate())),
      'CODIGO_SAP'                              => new sfWidgetFormFilterInput(),
      'DOCUMENTACION_COMPLETA'                  => new sfWidgetFormFilterInput(),
      'AVISO_CHECK_LIST'                        => new sfWidgetFormFilterInput(),
    ));

    $this->setValidators(array(
      'PROV_CORRESPONDIENTE_AUTOFACTURACION_ID' => new sfValidatorPropelChoice(array('required' => false, 'model' => 'ProvCorrespondienteAutofacturacion', 'column' => 'PROV_CORRESPONDIENTE_AUTOFACTURACION_ID')),
      'PROVEEDOR_ID'                            => new sfValidatorPropelChoice(array('required' => false, 'model' => 'Proveedor', 'column' => 'PROVEEDOR_ID')),
      'PROV_TIPO_INDUSTRIA_ID'                  => new sfValidatorPropelChoice(array('required' => false, 'model' => 'ProvTipoIndustria', 'column' => 'PROV_TIPO_INDUSTRIA_ID')),
      'PROV_CONDICION_EXPEDICION_ID'            => new sfValidatorPropelChoice(array('required' => false, 'model' => 'ProvCondicionExpedicion', 'column' => 'PROV_CONDICION_EXPEDICION_ID')),
      'PROV_RECHAZADA_CREACION2_ID'             => new sfValidatorPropelChoice(array('required' => false, 'model' => 'ProvRechazadaCreacion2', 'column' => 'PROV_RECHAZADA_CREACION2_ID')),
      'PROV_APROBACION_ID'                      => new sfValidatorPropelChoice(array('required' => false, 'model' => 'ProvAprobacion', 'column' => 'PROV_APROBACION_ID')),
      'PROV_REVISADO_CREACION_ID'               => new sfValidatorPropelChoice(array('required' => false, 'model' => 'ProvRevisadoCreacion', 'column' => 'PROV_REVISADO_CREACION_ID')),
      'PROV_CONDICION_PAGO_ID'                  => new sfValidatorPropelChoice(array('required' => false, 'model' => 'ProvCondicionPago', 'column' => 'PROV_CONDICION_PAGO_ID')),
      'PROV_GRUPO_TESORERIA_ID'                 => new sfValidatorPropelChoice(array('required' => false, 'model' => 'ProvGrupoTesoreria', 'column' => 'PROV_GRUPO_TESORERIA_ID')),
      'PRO_PROV_APROBACION_ID'                  => new sfValidatorPropelChoice(array('required' => false, 'model' => 'ProvAprobacion', 'column' => 'PROV_APROBACION_ID')),
      'PROV_ADUANA_ENTRADA_ID'                  => new sfValidatorPropelChoice(array('required' => false, 'model' => 'ProvAduanaEntrada', 'column' => 'PROV_ADUANA_ENTRADA_ID')),
      'PROV_RECHAZADA_CREACION_ID'              => new sfValidatorPropelChoice(array('required' => false, 'model' => 'ProvRechazadaCreacion', 'column' => 'PROV_RECHAZADA_CREACION_ID')),
      'PROB_APROBADO_PARA_CREACION_ID'          => new sfValidatorPropelChoice(array('required' => false, 'model' => 'ProbAprobadoParaCreacion', 'column' => 'PROB_APROBADO_PARA_CREACION_ID')),
      'PROV_GRUPO_ESQUEMA_ID'                   => new sfValidatorPropelChoice(array('required' => false, 'model' => 'ProvGrupoEsquema', 'column' => 'PROV_GRUPO_ESQUEMA_ID')),
      'PROV_MONEDA_PEDIDO_ID'                   => new sfValidatorPropelChoice(array('required' => false, 'model' => 'ProvMonedaPedido', 'column' => 'PROV_MONEDA_PEDIDO_ID')),
      'PROV_TIPO_CONTRIBUYENTE_ID'              => new sfValidatorPropelChoice(array('required' => false, 'model' => 'ProvTipoContribuyente', 'column' => 'PROV_TIPO_CONTRIBUYENTE_ID')),
      'PROV_CUENTA_ASOCIADA_ID'                 => new sfValidatorPropelChoice(array('required' => false, 'model' => 'ProvCuentaAsociada', 'column' => 'PROV_CUENTA_ASOCIADA_ID')),
      'FECHA_INICIAL'                           => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDate(array('required' => false)), 'to_date' => new sfValidatorDate(array('required' => false)))),
      'FECHA_FINAL'                             => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDate(array('required' => false)), 'to_date' => new sfValidatorDate(array('required' => false)))),
      'OBSERVACIONES_GENERALES'                 => new sfValidatorPass(array('required' => false)),
      'FECHA_CREACION_SAP'                      => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDate(array('required' => false)), 'to_date' => new sfValidatorDate(array('required' => false)))),
      'CODIGO_SAP'                              => new sfValidatorPass(array('required' => false)),
      'DOCUMENTACION_COMPLETA'                  => new sfValidatorPass(array('required' => false)),
      'AVISO_CHECK_LIST'                        => new sfValidatorPass(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('prov_periodo_validez_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'ProvPeriodoValidez';
  }

  public function getFields()
  {
    return array(
      'PROV_PERIODO_VALIDEZ_ID'                 => 'Number',
      'PROV_CORRESPONDIENTE_AUTOFACTURACION_ID' => 'ForeignKey',
      'PROVEEDOR_ID'                            => 'ForeignKey',
      'PROV_TIPO_INDUSTRIA_ID'                  => 'ForeignKey',
      'PROV_CONDICION_EXPEDICION_ID'            => 'ForeignKey',
      'PROV_RECHAZADA_CREACION2_ID'             => 'ForeignKey',
      'PROV_APROBACION_ID'                      => 'ForeignKey',
      'PROV_REVISADO_CREACION_ID'               => 'ForeignKey',
      'PROV_CONDICION_PAGO_ID'                  => 'ForeignKey',
      'PROV_GRUPO_TESORERIA_ID'                 => 'ForeignKey',
      'PRO_PROV_APROBACION_ID'                  => 'ForeignKey',
      'PROV_ADUANA_ENTRADA_ID'                  => 'ForeignKey',
      'PROV_RECHAZADA_CREACION_ID'              => 'ForeignKey',
      'PROB_APROBADO_PARA_CREACION_ID'          => 'ForeignKey',
      'PROV_GRUPO_ESQUEMA_ID'                   => 'ForeignKey',
      'PROV_MONEDA_PEDIDO_ID'                   => 'ForeignKey',
      'PROV_TIPO_CONTRIBUYENTE_ID'              => 'ForeignKey',
      'PROV_CUENTA_ASOCIADA_ID'                 => 'ForeignKey',
      'FECHA_INICIAL'                           => 'Date',
      'FECHA_FINAL'                             => 'Date',
      'OBSERVACIONES_GENERALES'                 => 'Text',
      'FECHA_CREACION_SAP'                      => 'Date',
      'CODIGO_SAP'                              => 'Text',
      'DOCUMENTACION_COMPLETA'                  => 'Text',
      'AVISO_CHECK_LIST'                        => 'Text',
    );
  }
}
