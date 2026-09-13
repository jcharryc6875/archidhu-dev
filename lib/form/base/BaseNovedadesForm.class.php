<?php

/**
 * Novedades form base class.
 *
 * @method Novedades getObject() Returns the current form's model object
 *
 * @package    ##PROJECT_NAME##
 * @subpackage form
 * @author     ##AUTHOR_NAME##
 */
abstract class BaseNovedadesForm extends BaseFormPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'NOVEDADES_ID'     => new sfWidgetFormInputHidden(),
      'USUARIO_ID'       => new sfWidgetFormPropelChoice(array('model' => 'Usuario', 'add_empty' => false)),
      'MODULO_ID'        => new sfWidgetFormPropelChoice(array('model' => 'Modulo', 'add_empty' => false)),
      'TIPOCONTROL_ID'   => new sfWidgetFormPropelChoice(array('model' => 'TipoControl', 'add_empty' => false)),
      'CODIGO_PRINCIPAL' => new sfWidgetFormInputText(),
      'DESCRIPCION'      => new sfWidgetFormInputText(),
      'FECHA_CREACION'   => new sfWidgetFormDateTime(),
      'RUTA'             => new sfWidgetFormTextarea(),
      'REMITENTE'        => new sfWidgetFormInputText(),
      'DESTINATARIO'     => new sfWidgetFormInputText(),
      'VALOR_FACTURA'    => new sfWidgetFormInputText(),
      'TIPO_DOCUMENTO'   => new sfWidgetFormInputText(),
      'GUIA'             => new sfWidgetFormInputText(),
      'NUMERO_FACTURA'   => new sfWidgetFormInputText(),
    ));

    $this->setValidators(array(
      'NOVEDADES_ID'     => new sfValidatorChoice(array('choices' => array($this->getObject()->getNovedadesId()), 'empty_value' => $this->getObject()->getNovedadesId(), 'required' => false)),
      'USUARIO_ID'       => new sfValidatorPropelChoice(array('model' => 'Usuario', 'column' => 'USUARIO_ID')),
      'MODULO_ID'        => new sfValidatorPropelChoice(array('model' => 'Modulo', 'column' => 'MODULO_ID')),
      'TIPOCONTROL_ID'   => new sfValidatorPropelChoice(array('model' => 'TipoControl', 'column' => 'TIPOCONTROL_ID')),
      'CODIGO_PRINCIPAL' => new sfValidatorInteger(array('min' => -2147483648, 'max' => 2147483647, 'required' => false)),
      'DESCRIPCION'      => new sfValidatorString(array('max_length' => 500, 'required' => false)),
      'FECHA_CREACION'   => new sfValidatorDateTime(array('required' => false)),
      'RUTA'             => new sfValidatorString(array('required' => false)),
      'REMITENTE'        => new sfValidatorString(array('max_length' => 500, 'required' => false)),
      'DESTINATARIO'     => new sfValidatorString(array('max_length' => 500, 'required' => false)),
      'VALOR_FACTURA'    => new sfValidatorString(array('max_length' => 200, 'required' => false)),
      'TIPO_DOCUMENTO'   => new sfValidatorString(array('max_length' => 500, 'required' => false)),
      'GUIA'             => new sfValidatorInteger(array('min' => -9.2233720368548E+18, 'max' => 9.2233720368548E+18, 'required' => false)),
      'NUMERO_FACTURA'   => new sfValidatorInteger(array('min' => -9.2233720368548E+18, 'max' => 9.2233720368548E+18, 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('novedades[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'Novedades';
  }


}
