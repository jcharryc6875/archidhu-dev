<?php

/**
 * Dependencia form base class.
 *
 * @method Dependencia getObject() Returns the current form's model object
 *
 * @package    ##PROJECT_NAME##
 * @subpackage form
 * @author     ##AUTHOR_NAME##
 */
abstract class BaseDependenciaForm extends BaseFormPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'DEPENDENCIA_ID'       => new sfWidgetFormInputHidden(),
      'ENTIDAD_ID'           => new sfWidgetFormPropelChoice(array('model' => 'Entidad', 'add_empty' => false)),
      'OFICINAPRODUCTORA_ID' => new sfWidgetFormPropelChoice(array('model' => 'OficinaProductora', 'add_empty' => false)),
      'NOMBRE'               => new sfWidgetFormInputText(),
      'CODIGO'               => new sfWidgetFormInputText(),
      'TIEMPO_PRESTAMO'      => new sfWidgetFormInputText(),
      'ES_ACTUAL'            => new sfWidgetFormInputText(),
      'TIPO_TABLA'           => new sfWidgetFormInputText(),
      'SECCION_ID'           => new sfWidgetFormInputText(),
      'SUBFONDO_ID'          => new sfWidgetFormInputText(),
    ));

    $this->setValidators(array(
      'DEPENDENCIA_ID'       => new sfValidatorChoice(array('choices' => array($this->getObject()->getDependenciaId()), 'empty_value' => $this->getObject()->getDependenciaId(), 'required' => false)),
      'ENTIDAD_ID'           => new sfValidatorPropelChoice(array('model' => 'Entidad', 'column' => 'ENTIDAD_ID')),
      'OFICINAPRODUCTORA_ID' => new sfValidatorPropelChoice(array('model' => 'OficinaProductora', 'column' => 'OFICINAPRODUCTORA_ID')),
      'NOMBRE'               => new sfValidatorString(array('max_length' => 300, 'required' => false)),
      'CODIGO'               => new sfValidatorString(array('max_length' => 50, 'required' => false)),
      'TIEMPO_PRESTAMO'      => new sfValidatorInteger(array('min' => -32768, 'max' => 32767, 'required' => false)),
      'ES_ACTUAL'            => new sfValidatorInteger(array('min' => -128, 'max' => 127, 'required' => false)),
      'TIPO_TABLA'           => new sfValidatorString(array('max_length' => 20, 'required' => false)),
      'SECCION_ID'           => new sfValidatorInteger(array('min' => -2147483648, 'max' => 2147483647, 'required' => false)),
      'SUBFONDO_ID'          => new sfValidatorInteger(array('min' => -2147483648, 'max' => 2147483647, 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('dependencia[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'Dependencia';
  }


}
