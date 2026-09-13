<?php

/**
 * Procedimiento form base class.
 *
 * @method Procedimiento getObject() Returns the current form's model object
 *
 * @package    ##PROJECT_NAME##
 * @subpackage form
 * @author     ##AUTHOR_NAME##
 */
abstract class BaseProcedimientoForm extends BaseFormPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'PROCEDIMIENTO_ID'     => new sfWidgetFormInputHidden(),
      'USUARIO_ID'           => new sfWidgetFormPropelChoice(array('model' => 'Usuario', 'add_empty' => false)),
      'TIPOPROCEDIMIENTO_ID' => new sfWidgetFormPropelChoice(array('model' => 'TipoProcedimiento', 'add_empty' => false)),
      'DEPENDENCIA_ID'       => new sfWidgetFormPropelChoice(array('model' => 'Dependencia', 'add_empty' => false)),
      'CODIGO'               => new sfWidgetFormInputText(),
      'NOMBRE'               => new sfWidgetFormInputText(),
      'DESCRIPCION'          => new sfWidgetFormTextarea(),
      'EXTENSION'            => new sfWidgetFormInputText(),
      'RUTA'                 => new sfWidgetFormInputText(),
      'VERSION'              => new sfWidgetFormInputText(),
      'FECHA_CREACION'       => new sfWidgetFormDateTime(),
      'CODIGO_VERSION'       => new sfWidgetFormInputText(),
      'ES_ULTIMA_VERSION'    => new sfWidgetFormInputText(),
      'TEXT_CAMBIOS'         => new sfWidgetFormTextarea(),
      'ESTADO_APROBACION'    => new sfWidgetFormInputText(),
    ));

    $this->setValidators(array(
      'PROCEDIMIENTO_ID'     => new sfValidatorChoice(array('choices' => array($this->getObject()->getProcedimientoId()), 'empty_value' => $this->getObject()->getProcedimientoId(), 'required' => false)),
      'USUARIO_ID'           => new sfValidatorPropelChoice(array('model' => 'Usuario', 'column' => 'USUARIO_ID')),
      'TIPOPROCEDIMIENTO_ID' => new sfValidatorPropelChoice(array('model' => 'TipoProcedimiento', 'column' => 'TIPOPROCEDIMIENTO_ID')),
      'DEPENDENCIA_ID'       => new sfValidatorPropelChoice(array('model' => 'Dependencia', 'column' => 'DEPENDENCIA_ID')),
      'CODIGO'               => new sfValidatorString(array('max_length' => 50, 'required' => false)),
      'NOMBRE'               => new sfValidatorString(array('max_length' => 800, 'required' => false)),
      'DESCRIPCION'          => new sfValidatorString(array('required' => false)),
      'EXTENSION'            => new sfValidatorString(array('max_length' => 10, 'required' => false)),
      'RUTA'                 => new sfValidatorString(array('max_length' => 4000, 'required' => false)),
      'VERSION'              => new sfValidatorNumber(array('required' => false)),
      'FECHA_CREACION'       => new sfValidatorDateTime(array('required' => false)),
      'CODIGO_VERSION'       => new sfValidatorInteger(array('min' => -2147483648, 'max' => 2147483647, 'required' => false)),
      'ES_ULTIMA_VERSION'    => new sfValidatorInteger(array('min' => -128, 'max' => 127, 'required' => false)),
      'TEXT_CAMBIOS'         => new sfValidatorString(array('required' => false)),
      'ESTADO_APROBACION'    => new sfValidatorString(array('max_length' => 10, 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('procedimiento[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'Procedimiento';
  }


}
