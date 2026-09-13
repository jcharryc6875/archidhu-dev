<?php

/**
 * RolUsuarioRecibida filter form base class.
 *
 * @package    simad
 * @subpackage filter
 * @author     Your name here
 */
abstract class BaseRolUsuarioRecibidaFormFilter extends BaseFormFilterPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'DESCRIPCION'          => new sfWidgetFormFilterInput(),
    ));

    $this->setValidators(array(
      'DESCRIPCION'          => new sfValidatorPass(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('rol_usuario_recibida_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'RolUsuarioRecibida';
  }

  public function getFields()
  {
    return array(
      'ROLUSUARIORECIBIDAID' => 'Number',
      'DESCRIPCION'          => 'Text',
    );
  }
}
