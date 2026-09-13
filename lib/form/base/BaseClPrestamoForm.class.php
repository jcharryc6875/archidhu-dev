<?php

/**
 * ClPrestamo form base class.
 *
 * @method ClPrestamo getObject() Returns the current form's model object
 *
 * @package    ##PROJECT_NAME##
 * @subpackage form
 * @author     ##AUTHOR_NAME##
 */
abstract class BaseClPrestamoForm extends BaseFormPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'CLPRESTAMO_ID'        => new sfWidgetFormInputHidden(),
      'REGIONAL_ID'          => new sfWidgetFormPropelChoice(array('model' => 'Regional', 'add_empty' => false)),
      'USUARIO_ID'           => new sfWidgetFormPropelChoice(array('model' => 'Usuario', 'add_empty' => false)),
      'CLESTADOPRESTAMO_ID'  => new sfWidgetFormPropelChoice(array('model' => 'ClEstadoPrestamo', 'add_empty' => false)),
      'PERIODO_ID'           => new sfWidgetFormPropelChoice(array('model' => 'Periodo', 'add_empty' => false)),
      'FECHA_PRESTAMO'       => new sfWidgetFormDateTime(),
      'FECHA_VENCIMIENTO'    => new sfWidgetFormDateTime(),
      'FECHA_DEVOLUCION'     => new sfWidgetFormDateTime(),
      'OBSERVACIONES'        => new sfWidgetFormInputText(),
      'CONSECUTIVO_REGIONAL' => new sfWidgetFormInputText(),
      'NUMERO_RADICACION'    => new sfWidgetFormInputText(),
    ));

    $this->setValidators(array(
      'CLPRESTAMO_ID'        => new sfValidatorChoice(array('choices' => array($this->getObject()->getClprestamoId()), 'empty_value' => $this->getObject()->getClprestamoId(), 'required' => false)),
      'REGIONAL_ID'          => new sfValidatorPropelChoice(array('model' => 'Regional', 'column' => 'REGIONAL_ID')),
      'USUARIO_ID'           => new sfValidatorPropelChoice(array('model' => 'Usuario', 'column' => 'USUARIO_ID')),
      'CLESTADOPRESTAMO_ID'  => new sfValidatorPropelChoice(array('model' => 'ClEstadoPrestamo', 'column' => 'CLESTADOPRESTAMO_ID')),
      'PERIODO_ID'           => new sfValidatorPropelChoice(array('model' => 'Periodo', 'column' => 'PERIODO_ID')),
      'FECHA_PRESTAMO'       => new sfValidatorDateTime(array('required' => false)),
      'FECHA_VENCIMIENTO'    => new sfValidatorDateTime(array('required' => false)),
      'FECHA_DEVOLUCION'     => new sfValidatorDateTime(array('required' => false)),
      'OBSERVACIONES'        => new sfValidatorString(array('max_length' => 200, 'required' => false)),
      'CONSECUTIVO_REGIONAL' => new sfValidatorString(array('max_length' => 200, 'required' => false)),
      'NUMERO_RADICACION'    => new sfValidatorInteger(array('min' => -2147483648, 'max' => 2147483647, 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('cl_prestamo[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'ClPrestamo';
  }


}
