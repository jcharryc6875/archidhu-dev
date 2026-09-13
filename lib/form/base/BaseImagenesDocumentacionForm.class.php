<?php

/**
 * ImagenesDocumentacion form base class.
 *
 * @method ImagenesDocumentacion getObject() Returns the current form's model object
 *
 * @package    ##PROJECT_NAME##
 * @subpackage form
 * @author     ##AUTHOR_NAME##
 */
abstract class BaseImagenesDocumentacionForm extends BaseFormPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'IMAGENESDOCUMENTACION_ID' => new sfWidgetFormInputHidden(),
      'DOCUMENTACION_ID'         => new sfWidgetFormPropelChoice(array('model' => 'Documentacion', 'add_empty' => false)),
      'DESCRIPCION'              => new sfWidgetFormInputText(),
      'RUTA'                     => new sfWidgetFormInputText(),
      'FOLIOS'                   => new sfWidgetFormInputText(),
      'VINCULADA'                => new sfWidgetFormInputText(),
    ));

    $this->setValidators(array(
      'IMAGENESDOCUMENTACION_ID' => new sfValidatorChoice(array('choices' => array($this->getObject()->getImagenesdocumentacionId()), 'empty_value' => $this->getObject()->getImagenesdocumentacionId(), 'required' => false)),
      'DOCUMENTACION_ID'         => new sfValidatorPropelChoice(array('model' => 'Documentacion', 'column' => 'DOCUMENTACION_ID')),
      'DESCRIPCION'              => new sfValidatorString(array('max_length' => 250, 'required' => false)),
      'RUTA'                     => new sfValidatorString(array('max_length' => 1000, 'required' => false)),
      'FOLIOS'                   => new sfValidatorInteger(array('min' => -2147483648, 'max' => 2147483647, 'required' => false)),
      'VINCULADA'                => new sfValidatorInteger(array('min' => -128, 'max' => 127, 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('imagenes_documentacion[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'ImagenesDocumentacion';
  }


}
