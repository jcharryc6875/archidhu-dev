<?php

/**
 * ProvAprobacion form base class.
 *
 * @method ProvAprobacion getObject() Returns the current form's model object
 *
 * @package    ##PROJECT_NAME##
 * @subpackage form
 * @author     ##AUTHOR_NAME##
 */
abstract class BaseProvAprobacionForm extends BaseFormPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'PROV_APROBACION_ID'        => new sfWidgetFormInputHidden(),
      'PROV_USUARIO_AREA_ID'      => new sfWidgetFormPropelChoice(array('model' => 'ProvUsuarioArea', 'add_empty' => false)),
      'PROV_PERIODO_VALIDEZ_ID'   => new sfWidgetFormPropelChoice(array('model' => 'ProvPeriodoValidez', 'add_empty' => false)),
      'PROV_ESTADO_APROBACION_ID' => new sfWidgetFormPropelChoice(array('model' => 'ProvEstadoAprobacion', 'add_empty' => false)),
      'FECHA_CREACION'            => new sfWidgetFormDateTime(),
      'OBSERVACIONES'             => new sfWidgetFormInputText(),
      'USUARIO_ID'                => new sfWidgetFormInputText(),
    ));

    $this->setValidators(array(
      'PROV_APROBACION_ID'        => new sfValidatorChoice(array('choices' => array($this->getObject()->getProvAprobacionId()), 'empty_value' => $this->getObject()->getProvAprobacionId(), 'required' => false)),
      'PROV_USUARIO_AREA_ID'      => new sfValidatorPropelChoice(array('model' => 'ProvUsuarioArea', 'column' => 'PROV_USUARIO_AREA_ID')),
      'PROV_PERIODO_VALIDEZ_ID'   => new sfValidatorPropelChoice(array('model' => 'ProvPeriodoValidez', 'column' => 'PROV_PERIODO_VALIDEZ_ID')),
      'PROV_ESTADO_APROBACION_ID' => new sfValidatorPropelChoice(array('model' => 'ProvEstadoAprobacion', 'column' => 'PROV_ESTADO_APROBACION_ID')),
      'FECHA_CREACION'            => new sfValidatorDateTime(array('required' => false)),
      'OBSERVACIONES'             => new sfValidatorString(array('max_length' => 200, 'required' => false)),
      'USUARIO_ID'                => new sfValidatorInteger(array('min' => -2147483648, 'max' => 2147483647, 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('prov_aprobacion[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'ProvAprobacion';
  }


}
