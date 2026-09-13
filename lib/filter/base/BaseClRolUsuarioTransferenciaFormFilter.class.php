<?php

/**
 * ClRolUsuarioTransferencia filter form base class.
 *
 * @package    simad
 * @subpackage filter
 * @author     Your name here
 */
abstract class BaseClRolUsuarioTransferenciaFormFilter extends BaseFormFilterPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'DESCRIPCION'                  => new sfWidgetFormFilterInput(),
    ));

    $this->setValidators(array(
      'DESCRIPCION'                  => new sfValidatorPass(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('cl_rol_usuario_transferencia_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'ClRolUsuarioTransferencia';
  }

  public function getFields()
  {
    return array(
      'CLROLUSUARIOTRANSFERENCIA_ID' => 'Number',
      'DESCRIPCION'                  => 'Text',
    );
  }
}
