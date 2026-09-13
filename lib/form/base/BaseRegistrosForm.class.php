<?php

/**
 * Registros form base class.
 *
 * @method Registros getObject() Returns the current form's model object
 *
 * @package    ##PROJECT_NAME##
 * @subpackage form
 * @author     ##AUTHOR_NAME##
 */
abstract class BaseRegistrosForm extends BaseFormPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'REGISTROS_ID'            => new sfWidgetFormInputHidden(),
      'DOCUMENTACION_ID'        => new sfWidgetFormPropelChoice(array('model' => 'Documentacion', 'add_empty' => false)),
      'TIPOESCALA_ID'           => new sfWidgetFormPropelChoice(array('model' => 'TipoEscala', 'add_empty' => false)),
      'CATEGORIAESCALA_ID'      => new sfWidgetFormPropelChoice(array('model' => 'CategoriaEscala', 'add_empty' => false)),
      'ESPECIALIDADREGISTRO_ID' => new sfWidgetFormPropelChoice(array('model' => 'EspecialidadRegistro', 'add_empty' => false)),
      'PAGINACION'              => new sfWidgetFormInputText(),
      'MENCION_ESCALA'          => new sfWidgetFormInputText(),
      'MENCION_PROYECCION'      => new sfWidgetFormInputText(),
      'MENCION_COORDENADAS'     => new sfWidgetFormInputText(),
      'TAMANO'                  => new sfWidgetFormInputText(),
    ));

    $this->setValidators(array(
      'REGISTROS_ID'            => new sfValidatorChoice(array('choices' => array($this->getObject()->getRegistrosId()), 'empty_value' => $this->getObject()->getRegistrosId(), 'required' => false)),
      'DOCUMENTACION_ID'        => new sfValidatorPropelChoice(array('model' => 'Documentacion', 'column' => 'DOCUMENTACION_ID')),
      'TIPOESCALA_ID'           => new sfValidatorPropelChoice(array('model' => 'TipoEscala', 'column' => 'TIPOESCALA_ID')),
      'CATEGORIAESCALA_ID'      => new sfValidatorPropelChoice(array('model' => 'CategoriaEscala', 'column' => 'CATEGORIAESCALA_ID')),
      'ESPECIALIDADREGISTRO_ID' => new sfValidatorPropelChoice(array('model' => 'EspecialidadRegistro', 'column' => 'ESPECIALIDADREGISTRO_ID')),
      'PAGINACION'              => new sfValidatorInteger(array('min' => -2147483648, 'max' => 2147483647, 'required' => false)),
      'MENCION_ESCALA'          => new sfValidatorString(array('max_length' => 30, 'required' => false)),
      'MENCION_PROYECCION'      => new sfValidatorString(array('max_length' => 30, 'required' => false)),
      'MENCION_COORDENADAS'     => new sfValidatorString(array('max_length' => 30, 'required' => false)),
      'TAMANO'                  => new sfValidatorNumber(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('registros[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'Registros';
  }


}
