<?php

/**
 * RolUsuarioRecibida form base class.
 *
 * @method RolUsuarioRecibida getObject() Returns the current form's model object
 *
 * @package    ##PROJECT_NAME##
 * @subpackage form
 * @author     ##AUTHOR_NAME##
 */
abstract class BaseRolUsuarioRecibidaForm extends BaseFormPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'ROLUSUARIORECIBIDAID' => new sfWidgetFormInputHidden(),
      'DESCRIPCION'          => new sfWidgetFormInputText(),
    ));

    $this->setValidators(array(
      'ROLUSUARIORECIBIDAID' => new sfValidatorChoice(array('choices' => array($this->getObject()->getRolusuariorecibidaid()), 'empty_value' => $this->getObject()->getRolusuariorecibidaid(), 'required' => false)),
      'DESCRIPCION'          => new sfValidatorString(array('max_length' => 250, 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('rol_usuario_recibida[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'RolUsuarioRecibida';
  }


}
