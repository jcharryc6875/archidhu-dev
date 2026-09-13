<?php

/**
 * AsignarServicio form base class.
 *
 * @method AsignarServicio getObject() Returns the current form's model object
 *
 * @package    ##PROJECT_NAME##
 * @subpackage form
 * @author     ##AUTHOR_NAME##
 */
abstract class BaseAsignarServicioForm extends BaseFormPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'ASIGNARSERVICIO_ID'     => new sfWidgetFormInputHidden(),
      'SERVICIO_ID'            => new sfWidgetFormPropelChoice(array('model' => 'Servicio', 'add_empty' => false)),
      'OBSERVACIONES'          => new sfWidgetFormTextarea(),
      'VALOR'                  => new sfWidgetFormInputText(),
      'FECHA_ASIGNACION'       => new sfWidgetFormDateTime(),
      'FECHA_EJECUCION'        => new sfWidgetFormDateTime(),
      'TEMP_USUARIOASIGNADO'   => new sfWidgetFormInputText(),
      'TEMP_REGIONAL'          => new sfWidgetFormInputText(),
      'TEMP_USUARIO_ASIGNADO2' => new sfWidgetFormInputText(),
      'TEMP_USUARIO_ASIGNA'    => new sfWidgetFormInputText(),
    ));

    $this->setValidators(array(
      'ASIGNARSERVICIO_ID'     => new sfValidatorChoice(array('choices' => array($this->getObject()->getAsignarservicioId()), 'empty_value' => $this->getObject()->getAsignarservicioId(), 'required' => false)),
      'SERVICIO_ID'            => new sfValidatorPropelChoice(array('model' => 'Servicio', 'column' => 'SERVICIO_ID')),
      'OBSERVACIONES'          => new sfValidatorString(array('required' => false)),
      'VALOR'                  => new sfValidatorInteger(array('min' => -2147483648, 'max' => 2147483647, 'required' => false)),
      'FECHA_ASIGNACION'       => new sfValidatorDateTime(array('required' => false)),
      'FECHA_EJECUCION'        => new sfValidatorDateTime(array('required' => false)),
      'TEMP_USUARIOASIGNADO'   => new sfValidatorInteger(array('min' => -2147483648, 'max' => 2147483647, 'required' => false)),
      'TEMP_REGIONAL'          => new sfValidatorInteger(array('min' => -2147483648, 'max' => 2147483647, 'required' => false)),
      'TEMP_USUARIO_ASIGNADO2' => new sfValidatorInteger(array('min' => -2147483648, 'max' => 2147483647, 'required' => false)),
      'TEMP_USUARIO_ASIGNA'    => new sfValidatorInteger(array('min' => -2147483648, 'max' => 2147483647, 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('asignar_servicio[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'AsignarServicio';
  }


}
