<?php

/**
 * DocSolicitudPrestamo form base class.
 *
 * @method DocSolicitudPrestamo getObject() Returns the current form's model object
 *
 * @package    ##PROJECT_NAME##
 * @subpackage form
 * @author     ##AUTHOR_NAME##
 */
abstract class BaseDocSolicitudPrestamoForm extends BaseFormPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'DOCSOLICITUDPRESTAMO_ID'       => new sfWidgetFormInputHidden(),
      'DOCUMENTACION_ID'              => new sfWidgetFormPropelChoice(array('model' => 'Documentacion', 'add_empty' => false)),
      'USUARIO_ID'                    => new sfWidgetFormPropelChoice(array('model' => 'Usuario', 'add_empty' => false)),
      'DOCSOLICITUDPRESTAMOESTADO_ID' => new sfWidgetFormPropelChoice(array('model' => 'DocSolicitudPrestamoEstado', 'add_empty' => false)),
      'FECHA_CREACION'                => new sfWidgetFormDateTime(),
      'FECHA_ATENCION'                => new sfWidgetFormDateTime(),
    ));

    $this->setValidators(array(
      'DOCSOLICITUDPRESTAMO_ID'       => new sfValidatorChoice(array('choices' => array($this->getObject()->getDocsolicitudprestamoId()), 'empty_value' => $this->getObject()->getDocsolicitudprestamoId(), 'required' => false)),
      'DOCUMENTACION_ID'              => new sfValidatorPropelChoice(array('model' => 'Documentacion', 'column' => 'DOCUMENTACION_ID')),
      'USUARIO_ID'                    => new sfValidatorPropelChoice(array('model' => 'Usuario', 'column' => 'USUARIO_ID')),
      'DOCSOLICITUDPRESTAMOESTADO_ID' => new sfValidatorPropelChoice(array('model' => 'DocSolicitudPrestamoEstado', 'column' => 'DOCSOLICITUDPRESTAMOESTADO_ID')),
      'FECHA_CREACION'                => new sfValidatorDateTime(array('required' => false)),
      'FECHA_ATENCION'                => new sfValidatorDateTime(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('doc_solicitud_prestamo[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'DocSolicitudPrestamo';
  }


}
