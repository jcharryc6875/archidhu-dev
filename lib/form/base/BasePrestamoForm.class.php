<?php

/**
 * Prestamo form base class.
 *
 * @method Prestamo getObject() Returns the current form's model object
 *
 * @package    ##PROJECT_NAME##
 * @subpackage form
 * @author     ##AUTHOR_NAME##
 */
abstract class BasePrestamoForm extends BaseFormPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'PRESTAMO_ID'          => new sfWidgetFormInputHidden(),
      'ESTADOPRESTAMO_ID'    => new sfWidgetFormPropelChoice(array('model' => 'EstadoPrestamo', 'add_empty' => false)),
      'USUARIO_ID'           => new sfWidgetFormPropelChoice(array('model' => 'Usuario', 'add_empty' => false)),
      'REGIONAL_ID'          => new sfWidgetFormPropelChoice(array('model' => 'Regional', 'add_empty' => false)),
      'PERIODO_ID'           => new sfWidgetFormPropelChoice(array('model' => 'Periodo', 'add_empty' => false)),
      'FECHA_PRESTAMO'       => new sfWidgetFormDateTime(),
      'FECHA_VENCIMIENTO'    => new sfWidgetFormDateTime(),
      'FECHA_DEVOLUCION'     => new sfWidgetFormDateTime(),
      'OBSERVACIONES'        => new sfWidgetFormInputText(),
      'CONSECUTIVO_REGIONAL' => new sfWidgetFormInputText(),
      'NUMERO_RADICACION'    => new sfWidgetFormInputText(),
    ));

    $this->setValidators(array(
      'PRESTAMO_ID'          => new sfValidatorChoice(array('choices' => array($this->getObject()->getPrestamoId()), 'empty_value' => $this->getObject()->getPrestamoId(), 'required' => false)),
      'ESTADOPRESTAMO_ID'    => new sfValidatorPropelChoice(array('model' => 'EstadoPrestamo', 'column' => 'ESTADOPRESTAMO_ID')),
      'USUARIO_ID'           => new sfValidatorPropelChoice(array('model' => 'Usuario', 'column' => 'USUARIO_ID')),
      'REGIONAL_ID'          => new sfValidatorPropelChoice(array('model' => 'Regional', 'column' => 'REGIONAL_ID')),
      'PERIODO_ID'           => new sfValidatorPropelChoice(array('model' => 'Periodo', 'column' => 'PERIODO_ID')),
      'FECHA_PRESTAMO'       => new sfValidatorDateTime(array('required' => false)),
      'FECHA_VENCIMIENTO'    => new sfValidatorDateTime(array('required' => false)),
      'FECHA_DEVOLUCION'     => new sfValidatorDateTime(array('required' => false)),
      'OBSERVACIONES'        => new sfValidatorString(array('max_length' => 200, 'required' => false)),
      'CONSECUTIVO_REGIONAL' => new sfValidatorString(array('max_length' => 200, 'required' => false)),
      'NUMERO_RADICACION'    => new sfValidatorInteger(array('min' => -2147483648, 'max' => 2147483647, 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('prestamo[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'Prestamo';
  }


}
