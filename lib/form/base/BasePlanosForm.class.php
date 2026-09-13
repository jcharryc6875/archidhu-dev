<?php

/**
 * Planos form base class.
 *
 * @method Planos getObject() Returns the current form's model object
 *
 * @package    ##PROJECT_NAME##
 * @subpackage form
 * @author     ##AUTHOR_NAME##
 */
abstract class BasePlanosForm extends BaseFormPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'PLANOS_ID'            => new sfWidgetFormInputHidden(),
      'ESPECIALIDADPLANO_ID' => new sfWidgetFormPropelChoice(array('model' => 'EspecialidadPlano', 'add_empty' => false)),
      'DOCUMENTACION_ID'     => new sfWidgetFormPropelChoice(array('model' => 'Documentacion', 'add_empty' => false)),
      'TIPOESCALA_ID'        => new sfWidgetFormPropelChoice(array('model' => 'TipoEscala', 'add_empty' => false)),
      'CATEGORIAESCALA_ID'   => new sfWidgetFormPropelChoice(array('model' => 'CategoriaEscala', 'add_empty' => false)),
      'VERSION'              => new sfWidgetFormInputText(),
      'PAGINACION'           => new sfWidgetFormInputText(),
      'ILUSTRACIONES'        => new sfWidgetFormInputText(),
      'MENCION_ESCALA'       => new sfWidgetFormInputText(),
      'MENCION_PROYECCION'   => new sfWidgetFormInputText(),
      'MENCION_COORDENADAS'  => new sfWidgetFormInputText(),
      'TAMANO'               => new sfWidgetFormInputText(),
    ));

    $this->setValidators(array(
      'PLANOS_ID'            => new sfValidatorChoice(array('choices' => array($this->getObject()->getPlanosId()), 'empty_value' => $this->getObject()->getPlanosId(), 'required' => false)),
      'ESPECIALIDADPLANO_ID' => new sfValidatorPropelChoice(array('model' => 'EspecialidadPlano', 'column' => 'ESPECIALIDADPLANO_ID')),
      'DOCUMENTACION_ID'     => new sfValidatorPropelChoice(array('model' => 'Documentacion', 'column' => 'DOCUMENTACION_ID')),
      'TIPOESCALA_ID'        => new sfValidatorPropelChoice(array('model' => 'TipoEscala', 'column' => 'TIPOESCALA_ID')),
      'CATEGORIAESCALA_ID'   => new sfValidatorPropelChoice(array('model' => 'CategoriaEscala', 'column' => 'CATEGORIAESCALA_ID')),
      'VERSION'              => new sfValidatorNumber(array('required' => false)),
      'PAGINACION'           => new sfValidatorInteger(array('min' => -2147483648, 'max' => 2147483647, 'required' => false)),
      'ILUSTRACIONES'        => new sfValidatorInteger(array('min' => -128, 'max' => 127, 'required' => false)),
      'MENCION_ESCALA'       => new sfValidatorString(array('max_length' => 30, 'required' => false)),
      'MENCION_PROYECCION'   => new sfValidatorString(array('max_length' => 30, 'required' => false)),
      'MENCION_COORDENADAS'  => new sfValidatorString(array('max_length' => 30, 'required' => false)),
      'TAMANO'               => new sfValidatorNumber(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('planos[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'Planos';
  }


}
