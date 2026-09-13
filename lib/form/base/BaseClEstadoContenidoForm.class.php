<?php

/**
 * ClEstadoContenido form base class.
 *
 * @method ClEstadoContenido getObject() Returns the current form's model object
 *
 * @package    ##PROJECT_NAME##
 * @subpackage form
 * @author     ##AUTHOR_NAME##
 */
abstract class BaseClEstadoContenidoForm extends BaseFormPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'CL_ESTADO_CONTENIDO_ID' => new sfWidgetFormInputHidden(),
      'DESCRIPCION'            => new sfWidgetFormInputText(),
    ));

    $this->setValidators(array(
      'CL_ESTADO_CONTENIDO_ID' => new sfValidatorChoice(array('choices' => array($this->getObject()->getClEstadoContenidoId()), 'empty_value' => $this->getObject()->getClEstadoContenidoId(), 'required' => false)),
      'DESCRIPCION'            => new sfValidatorString(array('max_length' => 250, 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('cl_estado_contenido[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'ClEstadoContenido';
  }


}
