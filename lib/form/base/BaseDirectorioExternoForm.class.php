<?php

/**
 * DirectorioExterno form base class.
 *
 * @method DirectorioExterno getObject() Returns the current form's model object
 *
 * @package    ##PROJECT_NAME##
 * @subpackage form
 * @author     ##AUTHOR_NAME##
 */
abstract class BaseDirectorioExternoForm extends BaseFormPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'DIRECTORIOEXTERNO_ID'  => new sfWidgetFormInputHidden(),
      'USUARIO_ID'            => new sfWidgetFormPropelChoice(array('model' => 'Usuario', 'add_empty' => false)),
      'ENTIDAD_ID'            => new sfWidgetFormPropelChoice(array('model' => 'Entidad', 'add_empty' => true)),
      'CIUDAD_ID'             => new sfWidgetFormPropelChoice(array('model' => 'Ciudad', 'add_empty' => false)),
      'TIPOPETICIONARIO_ID'   => new sfWidgetFormPropelChoice(array('model' => 'TipoPeticionario', 'add_empty' => true)),
      'TIPOIDENTIFICACION_ID' => new sfWidgetFormPropelChoice(array('model' => 'TipoIdentificacion', 'add_empty' => true)),
      'NOMBRE'                => new sfWidgetFormInputText(),
      'DIRECCION'             => new sfWidgetFormInputText(),
      'FUNCIONARIO'           => new sfWidgetFormInputText(),
      'CARGO'                 => new sfWidgetFormInputText(),
      'TELEFONO'              => new sfWidgetFormInputText(),
      'FAX'                   => new sfWidgetFormInputText(),
      'EMAIL'                 => new sfWidgetFormInputText(),
      'PREFIJO'               => new sfWidgetFormInputText(),
      'ES_PUBLICO'            => new sfWidgetFormInputText(),
      'NIT'                   => new sfWidgetFormInputText(),
      'BANCO_ID'              => new sfWidgetFormInputText(),
      'BANCO_ABREVIATURA'     => new sfWidgetFormInputText(),
      'BANCO_CODIGO'          => new sfWidgetFormInputText(),
      'ES_BANCO'              => new sfWidgetFormInputText(),
      'CONT_EDICION'          => new sfWidgetFormInputText(),
    ));

    $this->setValidators(array(
      'DIRECTORIOEXTERNO_ID'  => new sfValidatorChoice(array('choices' => array($this->getObject()->getDirectorioexternoId()), 'empty_value' => $this->getObject()->getDirectorioexternoId(), 'required' => false)),
      'USUARIO_ID'            => new sfValidatorPropelChoice(array('model' => 'Usuario', 'column' => 'USUARIO_ID')),
      'ENTIDAD_ID'            => new sfValidatorPropelChoice(array('model' => 'Entidad', 'column' => 'ENTIDAD_ID', 'required' => false)),
      'CIUDAD_ID'             => new sfValidatorPropelChoice(array('model' => 'Ciudad', 'column' => 'CIUDAD_ID')),
      'TIPOPETICIONARIO_ID'   => new sfValidatorPropelChoice(array('model' => 'TipoPeticionario', 'column' => 'TIPOPETICIONARIO_ID', 'required' => false)),
      'TIPOIDENTIFICACION_ID' => new sfValidatorPropelChoice(array('model' => 'TipoIdentificacion', 'column' => 'TIPOIDENTIFICACION_ID', 'required' => false)),
      'NOMBRE'                => new sfValidatorString(array('max_length' => 500, 'required' => false)),
      'DIRECCION'             => new sfValidatorString(array('max_length' => 300, 'required' => false)),
      'FUNCIONARIO'           => new sfValidatorString(array('max_length' => 300, 'required' => false)),
      'CARGO'                 => new sfValidatorString(array('max_length' => 300, 'required' => false)),
      'TELEFONO'              => new sfValidatorString(array('max_length' => 200, 'required' => false)),
      'FAX'                   => new sfValidatorString(array('max_length' => 200, 'required' => false)),
      'EMAIL'                 => new sfValidatorString(array('max_length' => 200, 'required' => false)),
      'PREFIJO'               => new sfValidatorString(array('max_length' => 80, 'required' => false)),
      'ES_PUBLICO'            => new sfValidatorInteger(array('min' => -128, 'max' => 127, 'required' => false)),
      'NIT'                   => new sfValidatorString(array('max_length' => 200, 'required' => false)),
      'BANCO_ID'              => new sfValidatorInteger(array('min' => -2147483648, 'max' => 2147483647, 'required' => false)),
      'BANCO_ABREVIATURA'     => new sfValidatorString(array('max_length' => 80, 'required' => false)),
      'BANCO_CODIGO'          => new sfValidatorString(array('max_length' => 80, 'required' => false)),
      'ES_BANCO'              => new sfValidatorInteger(array('min' => -2147483648, 'max' => 2147483647, 'required' => false)),
      'CONT_EDICION'          => new sfValidatorInteger(array('min' => -2147483648, 'max' => 2147483647, 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('directorio_externo[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'DirectorioExterno';
  }


}
