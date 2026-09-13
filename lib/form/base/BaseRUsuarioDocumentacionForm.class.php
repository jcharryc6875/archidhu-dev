<?php

/**
 * RUsuarioDocumentacion form base class.
 *
 * @method RUsuarioDocumentacion getObject() Returns the current form's model object
 *
 * @package    ##PROJECT_NAME##
 * @subpackage form
 * @author     ##AUTHOR_NAME##
 */
abstract class BaseRUsuarioDocumentacionForm extends BaseFormPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'RUSUARIODOCUMENTACION_ID' => new sfWidgetFormInputHidden(),
      'DESCRIPCION'              => new sfWidgetFormInputText(),
    ));

    $this->setValidators(array(
      'RUSUARIODOCUMENTACION_ID' => new sfValidatorChoice(array('choices' => array($this->getObject()->getRusuariodocumentacionId()), 'empty_value' => $this->getObject()->getRusuariodocumentacionId(), 'required' => false)),
      'DESCRIPCION'              => new sfValidatorString(array('max_length' => 250, 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('r_usuario_documentacion[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'RUsuarioDocumentacion';
  }


}
