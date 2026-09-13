<?php

/**
 * DocSolicitudPrestamoEstado form base class.
 *
 * @method DocSolicitudPrestamoEstado getObject() Returns the current form's model object
 *
 * @package    ##PROJECT_NAME##
 * @subpackage form
 * @author     ##AUTHOR_NAME##
 */
abstract class BaseDocSolicitudPrestamoEstadoForm extends BaseFormPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'DOCSOLICITUDPRESTAMOESTADO_ID' => new sfWidgetFormInputHidden(),
      'DESCRIPCION'                   => new sfWidgetFormInputText(),
    ));

    $this->setValidators(array(
      'DOCSOLICITUDPRESTAMOESTADO_ID' => new sfValidatorChoice(array('choices' => array($this->getObject()->getDocsolicitudprestamoestadoId()), 'empty_value' => $this->getObject()->getDocsolicitudprestamoestadoId(), 'required' => false)),
      'DESCRIPCION'                   => new sfValidatorString(array('max_length' => 250, 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('doc_solicitud_prestamo_estado[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'DocSolicitudPrestamoEstado';
  }


}
