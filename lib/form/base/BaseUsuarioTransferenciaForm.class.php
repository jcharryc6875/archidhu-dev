<?php

/**
 * UsuarioTransferencia form base class.
 *
 * @method UsuarioTransferencia getObject() Returns the current form's model object
 *
 * @package    ##PROJECT_NAME##
 * @subpackage form
 * @author     ##AUTHOR_NAME##
 */
abstract class BaseUsuarioTransferenciaForm extends BaseFormPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'USUARIOTRANSFERENCIA_ID'    => new sfWidgetFormInputHidden(),
      'ROLUSUARIOTRANSFERENCIA_ID' => new sfWidgetFormPropelChoice(array('model' => 'RolUsuariotransferencia', 'add_empty' => false)),
      'TRANSFERENCIA_ID'           => new sfWidgetFormPropelChoice(array('model' => 'Transferencia', 'add_empty' => false)),
      'USUARIO_ID'                 => new sfWidgetFormPropelChoice(array('model' => 'Usuario', 'add_empty' => false)),
    ));

    $this->setValidators(array(
      'USUARIOTRANSFERENCIA_ID'    => new sfValidatorChoice(array('choices' => array($this->getObject()->getUsuariotransferenciaId()), 'empty_value' => $this->getObject()->getUsuariotransferenciaId(), 'required' => false)),
      'ROLUSUARIOTRANSFERENCIA_ID' => new sfValidatorPropelChoice(array('model' => 'RolUsuariotransferencia', 'column' => 'ROLUSUARIOTRANSFERENCIA_ID')),
      'TRANSFERENCIA_ID'           => new sfValidatorPropelChoice(array('model' => 'Transferencia', 'column' => 'TRANSFERENCIA_ID')),
      'USUARIO_ID'                 => new sfValidatorPropelChoice(array('model' => 'Usuario', 'column' => 'USUARIO_ID')),
    ));

    $this->widgetSchema->setNameFormat('usuario_transferencia[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'UsuarioTransferencia';
  }


}
