<?php

/**
 * RolAutcomUsuario form base class.
 *
 * @method RolAutcomUsuario getObject() Returns the current form's model object
 *
 * @package    ##PROJECT_NAME##
 * @subpackage form
 * @author     ##AUTHOR_NAME##
 */
abstract class BaseRolAutcomUsuarioForm extends BaseFormPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'ROLAUTCOMUSUARIO_ID' => new sfWidgetFormInputHidden(),
      'DESCRIPCION'         => new sfWidgetFormInputText(),
    ));

    $this->setValidators(array(
      'ROLAUTCOMUSUARIO_ID' => new sfValidatorChoice(array('choices' => array($this->getObject()->getRolautcomusuarioId()), 'empty_value' => $this->getObject()->getRolautcomusuarioId(), 'required' => false)),
      'DESCRIPCION'         => new sfValidatorString(array('max_length' => 250, 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('rol_autcom_usuario[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'RolAutcomUsuario';
  }


}
