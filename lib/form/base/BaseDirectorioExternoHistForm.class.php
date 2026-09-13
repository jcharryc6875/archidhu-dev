<?php

/**
 * DirectorioExternoHist form base class.
 *
 * @method DirectorioExternoHist getObject() Returns the current form's model object
 *
 * @package    ##PROJECT_NAME##
 * @subpackage form
 * @author     ##AUTHOR_NAME##
 */
abstract class BaseDirectorioExternoHistForm extends BaseFormPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'DIRECTORIOEXTERNOHIST_ID' => new sfWidgetFormInputHidden(),
      'DIRECTORIOEXTERNO_ID'     => new sfWidgetFormPropelChoice(array('model' => 'DirectorioExterno', 'add_empty' => false)),
      'USUARIO_ID'               => new sfWidgetFormPropelChoice(array('model' => 'Usuario', 'add_empty' => false)),
      'CIUDAD_ID'                => new sfWidgetFormPropelChoice(array('model' => 'Ciudad', 'add_empty' => true)),
      'TIPOPETICIONARIO_ID'      => new sfWidgetFormPropelChoice(array('model' => 'TipoPeticionario', 'add_empty' => true)),
      'TIPOIDENTIFICACION_ID'    => new sfWidgetFormPropelChoice(array('model' => 'TipoIdentificacion', 'add_empty' => true)),
      'NOMBRE'                   => new sfWidgetFormTextarea(),
      'DIRECCION'                => new sfWidgetFormTextarea(),
      'FUNCIONARIO'              => new sfWidgetFormTextarea(),
      'CARGO'                    => new sfWidgetFormTextarea(),
      'TELEFONO'                 => new sfWidgetFormTextarea(),
      'FAX'                      => new sfWidgetFormTextarea(),
      'EMAIL'                    => new sfWidgetFormTextarea(),
      'PREFIJO'                  => new sfWidgetFormTextarea(),
      'ES_PUBLICO'               => new sfWidgetFormInputText(),
      'NIT'                      => new sfWidgetFormTextarea(),
      'BANCO_ID'                 => new sfWidgetFormInputText(),
      'BANCO_ABREVIATURA'        => new sfWidgetFormTextarea(),
      'BANCO_CODIGO'             => new sfWidgetFormTextarea(),
      'ES_BANCO'                 => new sfWidgetFormInputText(),
      'CONT_EDICION'             => new sfWidgetFormInputText(),
      'FECHA_MODIFICACION'       => new sfWidgetFormDateTime(),
    ));

    $this->setValidators(array(
      'DIRECTORIOEXTERNOHIST_ID' => new sfValidatorChoice(array('choices' => array($this->getObject()->getDirectorioexternohistId()), 'empty_value' => $this->getObject()->getDirectorioexternohistId(), 'required' => false)),
      'DIRECTORIOEXTERNO_ID'     => new sfValidatorPropelChoice(array('model' => 'DirectorioExterno', 'column' => 'DIRECTORIOEXTERNO_ID')),
      'USUARIO_ID'               => new sfValidatorPropelChoice(array('model' => 'Usuario', 'column' => 'USUARIO_ID')),
      'CIUDAD_ID'                => new sfValidatorPropelChoice(array('model' => 'Ciudad', 'column' => 'CIUDAD_ID', 'required' => false)),
      'TIPOPETICIONARIO_ID'      => new sfValidatorPropelChoice(array('model' => 'TipoPeticionario', 'column' => 'TIPOPETICIONARIO_ID', 'required' => false)),
      'TIPOIDENTIFICACION_ID'    => new sfValidatorPropelChoice(array('model' => 'TipoIdentificacion', 'column' => 'TIPOIDENTIFICACION_ID', 'required' => false)),
      'NOMBRE'                   => new sfValidatorString(array('required' => false)),
      'DIRECCION'                => new sfValidatorString(array('required' => false)),
      'FUNCIONARIO'              => new sfValidatorString(array('required' => false)),
      'CARGO'                    => new sfValidatorString(array('required' => false)),
      'TELEFONO'                 => new sfValidatorString(array('required' => false)),
      'FAX'                      => new sfValidatorString(array('required' => false)),
      'EMAIL'                    => new sfValidatorString(array('required' => false)),
      'PREFIJO'                  => new sfValidatorString(array('required' => false)),
      'ES_PUBLICO'               => new sfValidatorInteger(array('min' => -128, 'max' => 127, 'required' => false)),
      'NIT'                      => new sfValidatorString(array('required' => false)),
      'BANCO_ID'                 => new sfValidatorInteger(array('min' => -2147483648, 'max' => 2147483647, 'required' => false)),
      'BANCO_ABREVIATURA'        => new sfValidatorString(array('required' => false)),
      'BANCO_CODIGO'             => new sfValidatorString(array('required' => false)),
      'ES_BANCO'                 => new sfValidatorInteger(array('min' => -2147483648, 'max' => 2147483647, 'required' => false)),
      'CONT_EDICION'             => new sfValidatorInteger(array('min' => -2147483648, 'max' => 2147483647, 'required' => false)),
      'FECHA_MODIFICACION'       => new sfValidatorDateTime(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('directorio_externo_hist[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'DirectorioExternoHist';
  }


}
