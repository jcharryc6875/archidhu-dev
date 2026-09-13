<?php

/**
 * EstadoContenidoUnidadDocumental form base class.
 *
 * @method EstadoContenidoUnidadDocumental getObject() Returns the current form's model object
 *
 * @package    ##PROJECT_NAME##
 * @subpackage form
 * @author     ##AUTHOR_NAME##
 */
abstract class BaseEstadoContenidoUnidadDocumentalForm extends BaseFormPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'ESTADOCONTENIDOUNIDADDOC_ID' => new sfWidgetFormInputHidden(),
      'DESCRIPCION'                 => new sfWidgetFormInputText(),
    ));

    $this->setValidators(array(
      'ESTADOCONTENIDOUNIDADDOC_ID' => new sfValidatorChoice(array('choices' => array($this->getObject()->getEstadocontenidounidaddocId()), 'empty_value' => $this->getObject()->getEstadocontenidounidaddocId(), 'required' => false)),
      'DESCRIPCION'                 => new sfValidatorString(array('max_length' => 250, 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('estado_contenido_unidad_documental[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'EstadoContenidoUnidadDocumental';
  }


}
