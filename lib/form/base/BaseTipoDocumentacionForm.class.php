<?php

/**
 * TipoDocumentacion form base class.
 *
 * @method TipoDocumentacion getObject() Returns the current form's model object
 *
 * @package    ##PROJECT_NAME##
 * @subpackage form
 * @author     ##AUTHOR_NAME##
 */
abstract class BaseTipoDocumentacionForm extends BaseFormPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'TIPODOCUMENTACION_ID' => new sfWidgetFormInputHidden(),
      'DESCRIPCION'          => new sfWidgetFormInputText(),
    ));

    $this->setValidators(array(
      'TIPODOCUMENTACION_ID' => new sfValidatorChoice(array('choices' => array($this->getObject()->getTipodocumentacionId()), 'empty_value' => $this->getObject()->getTipodocumentacionId(), 'required' => false)),
      'DESCRIPCION'          => new sfValidatorString(array('max_length' => 250, 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('tipo_documentacion[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'TipoDocumentacion';
  }


}
