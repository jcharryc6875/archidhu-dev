<?php

/**
 * ComAprobacion form base class.
 *
 * @method ComAprobacion getObject() Returns the current form's model object
 *
 * @package    ##PROJECT_NAME##
 * @subpackage form
 * @author     ##AUTHOR_NAME##
 */
abstract class BaseComAprobacionForm extends BaseFormPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'COMAPROBACION_ID'       => new sfWidgetFormInputHidden(),
      'ESTADOCOMAPROBACION_ID' => new sfWidgetFormPropelChoice(array('model' => 'EstadoComAprobacion', 'add_empty' => false)),
      'USUARIO_ID'             => new sfWidgetFormPropelChoice(array('model' => 'Usuario', 'add_empty' => false)),
      'MODULO_ID'              => new sfWidgetFormPropelChoice(array('model' => 'Modulo', 'add_empty' => false)),
      'CONSECUTIVO_ID'         => new sfWidgetFormInputText(),
      'OBSERVACIONES'          => new sfWidgetFormInputText(),
      'FECHA_CREACION'         => new sfWidgetFormDateTime(),
      'FECHA_EJECUCION'        => new sfWidgetFormDateTime(),
    ));

    $this->setValidators(array(
      'COMAPROBACION_ID'       => new sfValidatorChoice(array('choices' => array($this->getObject()->getComaprobacionId()), 'empty_value' => $this->getObject()->getComaprobacionId(), 'required' => false)),
      'ESTADOCOMAPROBACION_ID' => new sfValidatorPropelChoice(array('model' => 'EstadoComAprobacion', 'column' => 'ESTADOCOMAPROBACION_ID')),
      'USUARIO_ID'             => new sfValidatorPropelChoice(array('model' => 'Usuario', 'column' => 'USUARIO_ID')),
      'MODULO_ID'              => new sfValidatorPropelChoice(array('model' => 'Modulo', 'column' => 'MODULO_ID')),
      'CONSECUTIVO_ID'         => new sfValidatorInteger(array('min' => -2147483648, 'max' => 2147483647)),
      'OBSERVACIONES'          => new sfValidatorInteger(array('min' => -2147483648, 'max' => 2147483647, 'required' => false)),
      'FECHA_CREACION'         => new sfValidatorDateTime(array('required' => false)),
      'FECHA_EJECUCION'        => new sfValidatorDateTime(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('com_aprobacion[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'ComAprobacion';
  }


}
