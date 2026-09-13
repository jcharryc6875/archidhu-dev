<?php

/**
 * WfActividadtransicionUsuario filter form base class.
 *
 * @package    simad
 * @subpackage filter
 * @author     Your name here
 */
abstract class BaseWfActividadtransicionUsuarioFormFilter extends BaseFormFilterPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'USUARIO_ID'                      => new sfWidgetFormPropelChoice(array('model' => 'Usuario', 'add_empty' => true)),
      'WFACTIVIDADTRANSICION_ID'        => new sfWidgetFormPropelChoice(array('model' => 'WfActividadTransicion', 'add_empty' => true)),
      'ENVIAR_ALERTA'                   => new sfWidgetFormFilterInput(),
    ));

    $this->setValidators(array(
      'USUARIO_ID'                      => new sfValidatorPropelChoice(array('required' => false, 'model' => 'Usuario', 'column' => 'USUARIO_ID')),
      'WFACTIVIDADTRANSICION_ID'        => new sfValidatorPropelChoice(array('required' => false, 'model' => 'WfActividadTransicion', 'column' => 'WFACTIVIDADTRANSICION_ID')),
      'ENVIAR_ALERTA'                   => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
    ));

    $this->widgetSchema->setNameFormat('wf_actividadtransicion_usuario_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'WfActividadtransicionUsuario';
  }

  public function getFields()
  {
    return array(
      'WFACTIVIDADTRANSICIONUSUARIO_ID' => 'Number',
      'USUARIO_ID'                      => 'ForeignKey',
      'WFACTIVIDADTRANSICION_ID'        => 'ForeignKey',
      'ENVIAR_ALERTA'                   => 'Number',
    );
  }
}
