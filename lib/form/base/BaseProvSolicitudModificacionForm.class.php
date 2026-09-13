<?php

/**
 * ProvSolicitudModificacion form base class.
 *
 * @method ProvSolicitudModificacion getObject() Returns the current form's model object
 *
 * @package    ##PROJECT_NAME##
 * @subpackage form
 * @author     ##AUTHOR_NAME##
 */
abstract class BaseProvSolicitudModificacionForm extends BaseFormPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'PROV_SOLICITUD_MODIFICACION_ID' => new sfWidgetFormInputHidden(),
      'PROV_ESTADO_SOL_MOD_ID'         => new sfWidgetFormPropelChoice(array('model' => 'ProvEstadoSolMod', 'add_empty' => false)),
      'PROVEEDOR_ID'                   => new sfWidgetFormPropelChoice(array('model' => 'Proveedor', 'add_empty' => false)),
      'USUARIO_ID'                     => new sfWidgetFormPropelChoice(array('model' => 'Usuario', 'add_empty' => false)),
      'FECHA_CREACION'                 => new sfWidgetFormDateTime(),
      'DESCRIPCION'                    => new sfWidgetFormInputText(),
      'FECHA_RESPUESTA'                => new sfWidgetFormDateTime(),
      'RESPUESTA_SOLICITUD'            => new sfWidgetFormInputText(),
    ));

    $this->setValidators(array(
      'PROV_SOLICITUD_MODIFICACION_ID' => new sfValidatorChoice(array('choices' => array($this->getObject()->getProvSolicitudModificacionId()), 'empty_value' => $this->getObject()->getProvSolicitudModificacionId(), 'required' => false)),
      'PROV_ESTADO_SOL_MOD_ID'         => new sfValidatorPropelChoice(array('model' => 'ProvEstadoSolMod', 'column' => 'PROV_ESTADO_SOL_MOD_ID')),
      'PROVEEDOR_ID'                   => new sfValidatorPropelChoice(array('model' => 'Proveedor', 'column' => 'PROVEEDOR_ID')),
      'USUARIO_ID'                     => new sfValidatorPropelChoice(array('model' => 'Usuario', 'column' => 'USUARIO_ID')),
      'FECHA_CREACION'                 => new sfValidatorDateTime(array('required' => false)),
      'DESCRIPCION'                    => new sfValidatorString(array('max_length' => 250, 'required' => false)),
      'FECHA_RESPUESTA'                => new sfValidatorDateTime(array('required' => false)),
      'RESPUESTA_SOLICITUD'            => new sfValidatorString(array('max_length' => 250, 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('prov_solicitud_modificacion[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'ProvSolicitudModificacion';
  }


}
