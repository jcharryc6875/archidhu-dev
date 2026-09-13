<?php

/**
 * Redireccion form base class.
 *
 * @method Redireccion getObject() Returns the current form's model object
 *
 * @package    ##PROJECT_NAME##
 * @subpackage form
 * @author     ##AUTHOR_NAME##
 */
abstract class BaseRedireccionForm extends BaseFormPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'REDIRECCION_ID'       => new sfWidgetFormInputHidden(),
      'ESTADOREDIRECCION_ID' => new sfWidgetFormPropelChoice(array('model' => 'EstadoRedireccion', 'add_empty' => false)),
      'MOTIVO'               => new sfWidgetFormInputText(),
      'FECHA_INICIAL'        => new sfWidgetFormDateTime(),
      'FECHA_FINAL'          => new sfWidgetFormDateTime(),
    ));

    $this->setValidators(array(
      'REDIRECCION_ID'       => new sfValidatorChoice(array('choices' => array($this->getObject()->getRedireccionId()), 'empty_value' => $this->getObject()->getRedireccionId(), 'required' => false)),
      'ESTADOREDIRECCION_ID' => new sfValidatorPropelChoice(array('model' => 'EstadoRedireccion', 'column' => 'ESTADOREDIRECCION_ID')),
      'MOTIVO'               => new sfValidatorString(array('max_length' => 100, 'required' => false)),
      'FECHA_INICIAL'        => new sfValidatorDateTime(array('required' => false)),
      'FECHA_FINAL'          => new sfValidatorDateTime(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('redireccion[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'Redireccion';
  }


}
