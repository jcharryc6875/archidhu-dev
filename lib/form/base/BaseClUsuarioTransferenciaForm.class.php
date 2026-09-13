<?php

/**
 * ClUsuarioTransferencia form base class.
 *
 * @method ClUsuarioTransferencia getObject() Returns the current form's model object
 *
 * @package    ##PROJECT_NAME##
 * @subpackage form
 * @author     ##AUTHOR_NAME##
 */
abstract class BaseClUsuarioTransferenciaForm extends BaseFormPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'CLUSUARIOTRANSFERENCIA_ID'    => new sfWidgetFormInputHidden(),
      'CLROLUSUARIOTRANSFERENCIA_ID' => new sfWidgetFormPropelChoice(array('model' => 'ClRolUsuarioTransferencia', 'add_empty' => false)),
      'USUARIO_ID'                   => new sfWidgetFormPropelChoice(array('model' => 'Usuario', 'add_empty' => false)),
      'CLTRANSFERENCIA_ID'           => new sfWidgetFormPropelChoice(array('model' => 'ClTransferencia', 'add_empty' => false)),
    ));

    $this->setValidators(array(
      'CLUSUARIOTRANSFERENCIA_ID'    => new sfValidatorChoice(array('choices' => array($this->getObject()->getClusuariotransferenciaId()), 'empty_value' => $this->getObject()->getClusuariotransferenciaId(), 'required' => false)),
      'CLROLUSUARIOTRANSFERENCIA_ID' => new sfValidatorPropelChoice(array('model' => 'ClRolUsuarioTransferencia', 'column' => 'CLROLUSUARIOTRANSFERENCIA_ID')),
      'USUARIO_ID'                   => new sfValidatorPropelChoice(array('model' => 'Usuario', 'column' => 'USUARIO_ID')),
      'CLTRANSFERENCIA_ID'           => new sfValidatorPropelChoice(array('model' => 'ClTransferencia', 'column' => 'CLTRANSFERENCIA_ID')),
    ));

    $this->widgetSchema->setNameFormat('cl_usuario_transferencia[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'ClUsuarioTransferencia';
  }


}
