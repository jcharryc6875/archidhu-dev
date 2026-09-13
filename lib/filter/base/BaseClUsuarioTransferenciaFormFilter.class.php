<?php

/**
 * ClUsuarioTransferencia filter form base class.
 *
 * @package    simad
 * @subpackage filter
 * @author     Your name here
 */
abstract class BaseClUsuarioTransferenciaFormFilter extends BaseFormFilterPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'CLROLUSUARIOTRANSFERENCIA_ID' => new sfWidgetFormPropelChoice(array('model' => 'ClRolUsuarioTransferencia', 'add_empty' => true)),
      'USUARIO_ID'                   => new sfWidgetFormPropelChoice(array('model' => 'Usuario', 'add_empty' => true)),
      'CLTRANSFERENCIA_ID'           => new sfWidgetFormPropelChoice(array('model' => 'ClTransferencia', 'add_empty' => true)),
    ));

    $this->setValidators(array(
      'CLROLUSUARIOTRANSFERENCIA_ID' => new sfValidatorPropelChoice(array('required' => false, 'model' => 'ClRolUsuarioTransferencia', 'column' => 'CLROLUSUARIOTRANSFERENCIA_ID')),
      'USUARIO_ID'                   => new sfValidatorPropelChoice(array('required' => false, 'model' => 'Usuario', 'column' => 'USUARIO_ID')),
      'CLTRANSFERENCIA_ID'           => new sfValidatorPropelChoice(array('required' => false, 'model' => 'ClTransferencia', 'column' => 'CLTRANSFERENCIA_ID')),
    ));

    $this->widgetSchema->setNameFormat('cl_usuario_transferencia_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'ClUsuarioTransferencia';
  }

  public function getFields()
  {
    return array(
      'CLUSUARIOTRANSFERENCIA_ID'    => 'Number',
      'CLROLUSUARIOTRANSFERENCIA_ID' => 'ForeignKey',
      'USUARIO_ID'                   => 'ForeignKey',
      'CLTRANSFERENCIA_ID'           => 'ForeignKey',
    );
  }
}
