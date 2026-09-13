<?php

/**
 * ProvSolicitudModificacion filter form base class.
 *
 * @package    simad
 * @subpackage filter
 * @author     Your name here
 */
abstract class BaseProvSolicitudModificacionFormFilter extends BaseFormFilterPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'PROV_ESTADO_SOL_MOD_ID'         => new sfWidgetFormPropelChoice(array('model' => 'ProvEstadoSolMod', 'add_empty' => true)),
      'PROVEEDOR_ID'                   => new sfWidgetFormPropelChoice(array('model' => 'Proveedor', 'add_empty' => true)),
      'USUARIO_ID'                     => new sfWidgetFormPropelChoice(array('model' => 'Usuario', 'add_empty' => true)),
      'FECHA_CREACION'                 => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate())),
      'DESCRIPCION'                    => new sfWidgetFormFilterInput(),
      'FECHA_RESPUESTA'                => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate())),
      'RESPUESTA_SOLICITUD'            => new sfWidgetFormFilterInput(),
    ));

    $this->setValidators(array(
      'PROV_ESTADO_SOL_MOD_ID'         => new sfValidatorPropelChoice(array('required' => false, 'model' => 'ProvEstadoSolMod', 'column' => 'PROV_ESTADO_SOL_MOD_ID')),
      'PROVEEDOR_ID'                   => new sfValidatorPropelChoice(array('required' => false, 'model' => 'Proveedor', 'column' => 'PROVEEDOR_ID')),
      'USUARIO_ID'                     => new sfValidatorPropelChoice(array('required' => false, 'model' => 'Usuario', 'column' => 'USUARIO_ID')),
      'FECHA_CREACION'                 => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDate(array('required' => false)), 'to_date' => new sfValidatorDate(array('required' => false)))),
      'DESCRIPCION'                    => new sfValidatorPass(array('required' => false)),
      'FECHA_RESPUESTA'                => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDate(array('required' => false)), 'to_date' => new sfValidatorDate(array('required' => false)))),
      'RESPUESTA_SOLICITUD'            => new sfValidatorPass(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('prov_solicitud_modificacion_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'ProvSolicitudModificacion';
  }

  public function getFields()
  {
    return array(
      'PROV_SOLICITUD_MODIFICACION_ID' => 'Number',
      'PROV_ESTADO_SOL_MOD_ID'         => 'ForeignKey',
      'PROVEEDOR_ID'                   => 'ForeignKey',
      'USUARIO_ID'                     => 'ForeignKey',
      'FECHA_CREACION'                 => 'Date',
      'DESCRIPCION'                    => 'Text',
      'FECHA_RESPUESTA'                => 'Date',
      'RESPUESTA_SOLICITUD'            => 'Text',
    );
  }
}
