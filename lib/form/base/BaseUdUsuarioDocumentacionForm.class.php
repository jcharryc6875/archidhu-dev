<?php

/**
 * UdUsuarioDocumentacion form base class.
 *
 * @method UdUsuarioDocumentacion getObject() Returns the current form's model object
 *
 * @package    ##PROJECT_NAME##
 * @subpackage form
 * @author     ##AUTHOR_NAME##
 */
abstract class BaseUdUsuarioDocumentacionForm extends BaseFormPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'UDUSUARIODOCUMENTACION_ID' => new sfWidgetFormInputHidden(),
      'DOCUMENTACION_ID'          => new sfWidgetFormPropelChoice(array('model' => 'Documentacion', 'add_empty' => false)),
      'RUSUARIODOCUMENTACION_ID'  => new sfWidgetFormPropelChoice(array('model' => 'RUsuarioDocumentacion', 'add_empty' => false)),
      'USUARIO_ID'                => new sfWidgetFormPropelChoice(array('model' => 'Usuario', 'add_empty' => false)),
    ));

    $this->setValidators(array(
      'UDUSUARIODOCUMENTACION_ID' => new sfValidatorChoice(array('choices' => array($this->getObject()->getUdusuariodocumentacionId()), 'empty_value' => $this->getObject()->getUdusuariodocumentacionId(), 'required' => false)),
      'DOCUMENTACION_ID'          => new sfValidatorPropelChoice(array('model' => 'Documentacion', 'column' => 'DOCUMENTACION_ID')),
      'RUSUARIODOCUMENTACION_ID'  => new sfValidatorPropelChoice(array('model' => 'RUsuarioDocumentacion', 'column' => 'RUSUARIODOCUMENTACION_ID')),
      'USUARIO_ID'                => new sfValidatorPropelChoice(array('model' => 'Usuario', 'column' => 'USUARIO_ID')),
    ));

    $this->widgetSchema->setNameFormat('ud_usuario_documentacion[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'UdUsuarioDocumentacion';
  }


}
