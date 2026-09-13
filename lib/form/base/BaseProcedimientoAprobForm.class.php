<?php

/**
 * ProcedimientoAprob form base class.
 *
 * @method ProcedimientoAprob getObject() Returns the current form's model object
 *
 * @package    ##PROJECT_NAME##
 * @subpackage form
 * @author     ##AUTHOR_NAME##
 */
abstract class BaseProcedimientoAprobForm extends BaseFormPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'PROCEDIMIENTOAPROB_ID' => new sfWidgetFormInputHidden(),
      'PROCEDIMIENTO_ID'      => new sfWidgetFormPropelChoice(array('model' => 'Procedimiento', 'add_empty' => false)),
      'USUARIO_ID'            => new sfWidgetFormPropelChoice(array('model' => 'Usuario', 'add_empty' => false)),
      'OBS_APROBACION'        => new sfWidgetFormInputText(),
      'ESTADO_APROBACION'     => new sfWidgetFormInputText(),
      'FECHA_CREACION'        => new sfWidgetFormDateTime(),
      'FECHA_APROBACION'      => new sfWidgetFormDateTime(),
    ));

    $this->setValidators(array(
      'PROCEDIMIENTOAPROB_ID' => new sfValidatorChoice(array('choices' => array($this->getObject()->getProcedimientoaprobId()), 'empty_value' => $this->getObject()->getProcedimientoaprobId(), 'required' => false)),
      'PROCEDIMIENTO_ID'      => new sfValidatorPropelChoice(array('model' => 'Procedimiento', 'column' => 'PROCEDIMIENTO_ID')),
      'USUARIO_ID'            => new sfValidatorPropelChoice(array('model' => 'Usuario', 'column' => 'USUARIO_ID')),
      'OBS_APROBACION'        => new sfValidatorString(array('max_length' => 4000, 'required' => false)),
      'ESTADO_APROBACION'     => new sfValidatorInteger(array('min' => -2147483648, 'max' => 2147483647)),
      'FECHA_CREACION'        => new sfValidatorDateTime(array('required' => false)),
      'FECHA_APROBACION'      => new sfValidatorDateTime(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('procedimiento_aprob[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'ProcedimientoAprob';
  }


}
