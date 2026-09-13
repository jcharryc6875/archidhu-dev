<?php

/**
 * EspecialidadRegistro form base class.
 *
 * @method EspecialidadRegistro getObject() Returns the current form's model object
 *
 * @package    ##PROJECT_NAME##
 * @subpackage form
 * @author     ##AUTHOR_NAME##
 */
abstract class BaseEspecialidadRegistroForm extends BaseFormPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'ESPECIALIDADREGISTRO_ID' => new sfWidgetFormInputHidden(),
      'DESCRIPCION'             => new sfWidgetFormInputText(),
    ));

    $this->setValidators(array(
      'ESPECIALIDADREGISTRO_ID' => new sfValidatorChoice(array('choices' => array($this->getObject()->getEspecialidadregistroId()), 'empty_value' => $this->getObject()->getEspecialidadregistroId(), 'required' => false)),
      'DESCRIPCION'             => new sfValidatorString(array('max_length' => 250, 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('especialidad_registro[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'EspecialidadRegistro';
  }


}
