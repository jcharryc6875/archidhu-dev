<?php

/**
 * AsignacionPqr form base class.
 *
 * @method AsignacionPqr getObject() Returns the current form's model object
 *
 * @package    ##PROJECT_NAME##
 * @subpackage form
 * @author     ##AUTHOR_NAME##
 */
abstract class BaseAsignacionPqrForm extends BaseFormPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'ASIGNACIONPQR_ID'       => new sfWidgetFormInputHidden(),
      'ESTADOASIGNACIONPQR_ID' => new sfWidgetFormPropelChoice(array('model' => 'EstadoAsignacionPqr', 'add_empty' => false)),
      'USUARIO_ID'             => new sfWidgetFormPropelChoice(array('model' => 'Usuario', 'add_empty' => false)),
      'PQR_ID'                 => new sfWidgetFormPropelChoice(array('model' => 'Pqr', 'add_empty' => false)),
      'FECHA_CREACION'         => new sfWidgetFormDateTime(),
      'ESTA_ASIGNADO'          => new sfWidgetFormInputText(),
    ));

    $this->setValidators(array(
      'ASIGNACIONPQR_ID'       => new sfValidatorChoice(array('choices' => array($this->getObject()->getAsignacionpqrId()), 'empty_value' => $this->getObject()->getAsignacionpqrId(), 'required' => false)),
      'ESTADOASIGNACIONPQR_ID' => new sfValidatorPropelChoice(array('model' => 'EstadoAsignacionPqr', 'column' => 'ESTADOASIGNACIONPQR_ID')),
      'USUARIO_ID'             => new sfValidatorPropelChoice(array('model' => 'Usuario', 'column' => 'USUARIO_ID')),
      'PQR_ID'                 => new sfValidatorPropelChoice(array('model' => 'Pqr', 'column' => 'PQR_ID')),
      'FECHA_CREACION'         => new sfValidatorDateTime(array('required' => false)),
      'ESTA_ASIGNADO'          => new sfValidatorInteger(array('min' => -128, 'max' => 127, 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('asignacion_pqr[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'AsignacionPqr';
  }


}
