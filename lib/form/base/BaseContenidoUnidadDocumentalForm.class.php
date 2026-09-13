<?php

/**
 * ContenidoUnidadDocumental form base class.
 *
 * @method ContenidoUnidadDocumental getObject() Returns the current form's model object
 *
 * @package    ##PROJECT_NAME##
 * @subpackage form
 * @author     ##AUTHOR_NAME##
 */
abstract class BaseContenidoUnidadDocumentalForm extends BaseFormPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'CONTENIDOUNIDADDOCUMENTAL_ID' => new sfWidgetFormInputHidden(),
      'UNIDADDOCUMENTAL_ID'          => new sfWidgetFormPropelChoice(array('model' => 'UnidadDocumental', 'add_empty' => false)),
      'VERIFICACIONCONTUNIDADDOC_ID' => new sfWidgetFormPropelChoice(array('model' => 'VerificacionContUnidadDoc', 'add_empty' => false)),
      'TIPODOCUMENTAL_ID'            => new sfWidgetFormPropelChoice(array('model' => 'TipoDocumental', 'add_empty' => false)),
      'ESTADOCONTENIDOUNIDADDOC_ID'  => new sfWidgetFormPropelChoice(array('model' => 'EstadoContenidoUnidadDocumental', 'add_empty' => false)),
      'USUARIO_ID'                   => new sfWidgetFormPropelChoice(array('model' => 'Usuario', 'add_empty' => false)),
      'DESCRIPCION'                  => new sfWidgetFormInputText(),
      'RUTA'                         => new sfWidgetFormTextarea(),
      'FECHA_CREACION'               => new sfWidgetFormDateTime(),
      'FOLIOS'                       => new sfWidgetFormInputText(),
      'CREADO_POR_WEB'               => new sfWidgetFormInputText(),
      'FECHA_DOCUMENTO'              => new sfWidgetFormDateTime(),
      'MARCA'                        => new sfWidgetFormInputText(),
      'SOPORTEUNIDADDOCUMENTAL_ID'   => new sfWidgetFormInputText(),
      'TIPOFIRMADIGITAL_ID'          => new sfWidgetFormInputText(),
      'VINCULO_REGISTRO'             => new sfWidgetFormInputText(),
    ));

    $this->setValidators(array(
      'CONTENIDOUNIDADDOCUMENTAL_ID' => new sfValidatorChoice(array('choices' => array($this->getObject()->getContenidounidaddocumentalId()), 'empty_value' => $this->getObject()->getContenidounidaddocumentalId(), 'required' => false)),
      'UNIDADDOCUMENTAL_ID'          => new sfValidatorPropelChoice(array('model' => 'UnidadDocumental', 'column' => 'UNIDADDOCUMENTAL_ID')),
      'VERIFICACIONCONTUNIDADDOC_ID' => new sfValidatorPropelChoice(array('model' => 'VerificacionContUnidadDoc', 'column' => 'VERIFICACIONCONTUNIDADDOC_ID')),
      'TIPODOCUMENTAL_ID'            => new sfValidatorPropelChoice(array('model' => 'TipoDocumental', 'column' => 'TIPODOCUMENTAL_ID')),
      'ESTADOCONTENIDOUNIDADDOC_ID'  => new sfValidatorPropelChoice(array('model' => 'EstadoContenidoUnidadDocumental', 'column' => 'ESTADOCONTENIDOUNIDADDOC_ID')),
      'USUARIO_ID'                   => new sfValidatorPropelChoice(array('model' => 'Usuario', 'column' => 'USUARIO_ID')),
      'DESCRIPCION'                  => new sfValidatorString(array('max_length' => 500, 'required' => false)),
      'RUTA'                         => new sfValidatorString(array('required' => false)),
      'FECHA_CREACION'               => new sfValidatorDateTime(array('required' => false)),
      'FOLIOS'                       => new sfValidatorInteger(array('min' => -2147483648, 'max' => 2147483647, 'required' => false)),
      'CREADO_POR_WEB'               => new sfValidatorInteger(array('min' => -128, 'max' => 127, 'required' => false)),
      'FECHA_DOCUMENTO'              => new sfValidatorDateTime(array('required' => false)),
      'MARCA'                        => new sfValidatorInteger(array('min' => -2147483648, 'max' => 2147483647, 'required' => false)),
      'SOPORTEUNIDADDOCUMENTAL_ID'   => new sfValidatorInteger(array('min' => -2147483648, 'max' => 2147483647, 'required' => false)),
      'TIPOFIRMADIGITAL_ID'          => new sfValidatorInteger(array('min' => -2147483648, 'max' => 2147483647, 'required' => false)),
      'VINCULO_REGISTRO'             => new sfValidatorInteger(array('min' => -128, 'max' => 127, 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('contenido_unidad_documental[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'ContenidoUnidadDocumental';
  }


}
