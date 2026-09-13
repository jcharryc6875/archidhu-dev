<?php

/**
 * ProvPeriodoValidez form base class.
 *
 * @method ProvPeriodoValidez getObject() Returns the current form's model object
 *
 * @package    ##PROJECT_NAME##
 * @subpackage form
 * @author     ##AUTHOR_NAME##
 */
abstract class BaseProvPeriodoValidezForm extends BaseFormPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'PROV_PERIODO_VALIDEZ_ID'                 => new sfWidgetFormInputHidden(),
      'PROV_CORRESPONDIENTE_AUTOFACTURACION_ID' => new sfWidgetFormPropelChoice(array('model' => 'ProvCorrespondienteAutofacturacion', 'add_empty' => true)),
      'PROVEEDOR_ID'                            => new sfWidgetFormPropelChoice(array('model' => 'Proveedor', 'add_empty' => false)),
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
      'FECHA_INICIAL'                           => new sfWidgetFormDateTime(),
      'FECHA_FINAL'                             => new sfWidgetFormDateTime(),
      'OBSERVACIONES_GENERALES'                 => new sfWidgetFormTextarea(),
      'FECHA_CREACION_SAP'                      => new sfWidgetFormDateTime(),
      'CODIGO_SAP'                              => new sfWidgetFormInputText(),
      'DOCUMENTACION_COMPLETA'                  => new sfWidgetFormInputText(),
      'AVISO_CHECK_LIST'                        => new sfWidgetFormInputText(),
    ));

    $this->setValidators(array(
      'PROV_PERIODO_VALIDEZ_ID'                 => new sfValidatorChoice(array('choices' => array($this->getObject()->getProvPeriodoValidezId()), 'empty_value' => $this->getObject()->getProvPeriodoValidezId(), 'required' => false)),
      'PROV_CORRESPONDIENTE_AUTOFACTURACION_ID' => new sfValidatorPropelChoice(array('model' => 'ProvCorrespondienteAutofacturacion', 'column' => 'PROV_CORRESPONDIENTE_AUTOFACTURACION_ID', 'required' => false)),
      'PROVEEDOR_ID'                            => new sfValidatorPropelChoice(array('model' => 'Proveedor', 'column' => 'PROVEEDOR_ID')),
      'PROV_TIPO_INDUSTRIA_ID'                  => new sfValidatorPropelChoice(array('model' => 'ProvTipoIndustria', 'column' => 'PROV_TIPO_INDUSTRIA_ID', 'required' => false)),
      'PROV_CONDICION_EXPEDICION_ID'            => new sfValidatorPropelChoice(array('model' => 'ProvCondicionExpedicion', 'column' => 'PROV_CONDICION_EXPEDICION_ID', 'required' => false)),
      'PROV_RECHAZADA_CREACION2_ID'             => new sfValidatorPropelChoice(array('model' => 'ProvRechazadaCreacion2', 'column' => 'PROV_RECHAZADA_CREACION2_ID', 'required' => false)),
      'PROV_APROBACION_ID'                      => new sfValidatorPropelChoice(array('model' => 'ProvAprobacion', 'column' => 'PROV_APROBACION_ID', 'required' => false)),
      'PROV_REVISADO_CREACION_ID'               => new sfValidatorPropelChoice(array('model' => 'ProvRevisadoCreacion', 'column' => 'PROV_REVISADO_CREACION_ID', 'required' => false)),
      'PROV_CONDICION_PAGO_ID'                  => new sfValidatorPropelChoice(array('model' => 'ProvCondicionPago', 'column' => 'PROV_CONDICION_PAGO_ID', 'required' => false)),
      'PROV_GRUPO_TESORERIA_ID'                 => new sfValidatorPropelChoice(array('model' => 'ProvGrupoTesoreria', 'column' => 'PROV_GRUPO_TESORERIA_ID', 'required' => false)),
      'PRO_PROV_APROBACION_ID'                  => new sfValidatorPropelChoice(array('model' => 'ProvAprobacion', 'column' => 'PROV_APROBACION_ID', 'required' => false)),
      'PROV_ADUANA_ENTRADA_ID'                  => new sfValidatorPropelChoice(array('model' => 'ProvAduanaEntrada', 'column' => 'PROV_ADUANA_ENTRADA_ID', 'required' => false)),
      'PROV_RECHAZADA_CREACION_ID'              => new sfValidatorPropelChoice(array('model' => 'ProvRechazadaCreacion', 'column' => 'PROV_RECHAZADA_CREACION_ID', 'required' => false)),
      'PROB_APROBADO_PARA_CREACION_ID'          => new sfValidatorPropelChoice(array('model' => 'ProbAprobadoParaCreacion', 'column' => 'PROB_APROBADO_PARA_CREACION_ID', 'required' => false)),
      'PROV_GRUPO_ESQUEMA_ID'                   => new sfValidatorPropelChoice(array('model' => 'ProvGrupoEsquema', 'column' => 'PROV_GRUPO_ESQUEMA_ID', 'required' => false)),
      'PROV_MONEDA_PEDIDO_ID'                   => new sfValidatorPropelChoice(array('model' => 'ProvMonedaPedido', 'column' => 'PROV_MONEDA_PEDIDO_ID', 'required' => false)),
      'PROV_TIPO_CONTRIBUYENTE_ID'              => new sfValidatorPropelChoice(array('model' => 'ProvTipoContribuyente', 'column' => 'PROV_TIPO_CONTRIBUYENTE_ID', 'required' => false)),
      'PROV_CUENTA_ASOCIADA_ID'                 => new sfValidatorPropelChoice(array('model' => 'ProvCuentaAsociada', 'column' => 'PROV_CUENTA_ASOCIADA_ID', 'required' => false)),
      'FECHA_INICIAL'                           => new sfValidatorDateTime(array('required' => false)),
      'FECHA_FINAL'                             => new sfValidatorDateTime(array('required' => false)),
      'OBSERVACIONES_GENERALES'                 => new sfValidatorString(array('required' => false)),
      'FECHA_CREACION_SAP'                      => new sfValidatorDateTime(array('required' => false)),
      'CODIGO_SAP'                              => new sfValidatorString(array('max_length' => 50, 'required' => false)),
      'DOCUMENTACION_COMPLETA'                  => new sfValidatorString(array('max_length' => 2, 'required' => false)),
      'AVISO_CHECK_LIST'                        => new sfValidatorString(array('max_length' => 2, 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('prov_periodo_validez[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'ProvPeriodoValidez';
  }


}
