<?php

/**
 * WfActividadtransicionUsuario form base class.
 *
 * @package    form
 * @subpackage wf_actividadtransicion_usuario
 * @version    SVN: $Id: sfPropelFormGeneratedTemplate.php 15484 2009-02-13 13:13:51Z fabien $
 */
class BaseWfActividadtransicionUsuarioForm extends BaseFormPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'wfactividadtransicionusuario_id' => new sfWidgetFormInputHidden(),
      'usuario_id'                      => new sfWidgetFormPropelSelect(array('model' => 'Usuario', 'add_empty' => false)),
      'wfactividadtransicion_id'        => new sfWidgetFormPropelSelect(array('model' => 'WfActividadTransicion', 'add_empty' => false)),
    ));

    $this->setValidators(array(
      'wfactividadtransicionusuario_id' => new sfValidatorPropelChoice(array('model' => 'WfActividadtransicionUsuario', 'column' => 'wfactividadtransicionusuario_id', 'required' => false)),
      'usuario_id'                      => new sfValidatorPropelChoice(array('model' => 'Usuario', 'column' => 'usuario_id')),
      'wfactividadtransicion_id'        => new sfValidatorPropelChoice(array('model' => 'WfActividadTransicion', 'column' => 'wfactividadtransicion_id')),
    ));

    $this->widgetSchema->setNameFormat('wf_actividadtransicion_usuario[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'WfActividadtransicionUsuario';
  }


}
