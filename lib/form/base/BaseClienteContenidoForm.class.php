<?php

/**
 * ClienteContenido form base class.
 *
 * @method ClienteContenido getObject() Returns the current form's model object
 *
 * @package    ##PROJECT_NAME##
 * @subpackage form
 * @author     ##AUTHOR_NAME##
 */
abstract class BaseClienteContenidoForm extends BaseFormPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'CLIENTE_CONTENIDO_ID'       => new sfWidgetFormInputHidden(),
      'USUARIO_ID'                 => new sfWidgetFormPropelChoice(array('model' => 'Usuario', 'add_empty' => false)),
      'VERIFICACIONCONTCLIENTE_ID' => new sfWidgetFormPropelChoice(array('model' => 'VerificacionContcliente', 'add_empty' => false)),
      'TIPODOCUMENTAL_ID'          => new sfWidgetFormPropelChoice(array('model' => 'TipoDocumental', 'add_empty' => false)),
      'CLIENTE_ID'                 => new sfWidgetFormPropelChoice(array('model' => 'Cliente', 'add_empty' => false)),
      'CL_ESTADO_CONTENIDO_ID'     => new sfWidgetFormPropelChoice(array('model' => 'ClEstadoContenido', 'add_empty' => false)),
      'DESCRIPCION'                => new sfWidgetFormInputText(),
      'RUTA'                       => new sfWidgetFormInputText(),
      'FECHA_CREACION'             => new sfWidgetFormDateTime(),
      'FOLIOS'                     => new sfWidgetFormInputText(),
      'CREADO_POR_WEB'             => new sfWidgetFormInputText(),
    ));

    $this->setValidators(array(
      'CLIENTE_CONTENIDO_ID'       => new sfValidatorChoice(array('choices' => array($this->getObject()->getClienteContenidoId()), 'empty_value' => $this->getObject()->getClienteContenidoId(), 'required' => false)),
      'USUARIO_ID'                 => new sfValidatorPropelChoice(array('model' => 'Usuario', 'column' => 'USUARIO_ID')),
      'VERIFICACIONCONTCLIENTE_ID' => new sfValidatorPropelChoice(array('model' => 'VerificacionContcliente', 'column' => 'VERIFICACIONCONTCLIENTE_ID')),
      'TIPODOCUMENTAL_ID'          => new sfValidatorPropelChoice(array('model' => 'TipoDocumental', 'column' => 'TIPODOCUMENTAL_ID')),
      'CLIENTE_ID'                 => new sfValidatorPropelChoice(array('model' => 'Cliente', 'column' => 'CLIENTE_ID')),
      'CL_ESTADO_CONTENIDO_ID'     => new sfValidatorPropelChoice(array('model' => 'ClEstadoContenido', 'column' => 'CL_ESTADO_CONTENIDO_ID')),
      'DESCRIPCION'                => new sfValidatorString(array('max_length' => 250, 'required' => false)),
      'RUTA'                       => new sfValidatorString(array('max_length' => 1000, 'required' => false)),
      'FECHA_CREACION'             => new sfValidatorDateTime(array('required' => false)),
      'FOLIOS'                     => new sfValidatorInteger(array('min' => -2147483648, 'max' => 2147483647, 'required' => false)),
      'CREADO_POR_WEB'             => new sfValidatorInteger(array('min' => -128, 'max' => 127, 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('cliente_contenido[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'ClienteContenido';
  }


}
