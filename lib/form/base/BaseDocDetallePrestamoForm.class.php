<?php

/**
 * DocDetallePrestamo form base class.
 *
 * @method DocDetallePrestamo getObject() Returns the current form's model object
 *
 * @package    ##PROJECT_NAME##
 * @subpackage form
 * @author     ##AUTHOR_NAME##
 */
abstract class BaseDocDetallePrestamoForm extends BaseFormPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'DOCDETALLEPRESTAMO_ID'   => new sfWidgetFormInputHidden(),
      'DOCPRESTAMO_ID'          => new sfWidgetFormPropelChoice(array('model' => 'DocPrestamo', 'add_empty' => false)),
      'DOCSOLICITUDPRESTAMO_ID' => new sfWidgetFormPropelChoice(array('model' => 'DocSolicitudPrestamo', 'add_empty' => false)),
      'DOCESTADOPRESTAMO_ID'    => new sfWidgetFormPropelChoice(array('model' => 'DocEstadoPrestamo', 'add_empty' => false)),
    ));

    $this->setValidators(array(
      'DOCDETALLEPRESTAMO_ID'   => new sfValidatorChoice(array('choices' => array($this->getObject()->getDocdetalleprestamoId()), 'empty_value' => $this->getObject()->getDocdetalleprestamoId(), 'required' => false)),
      'DOCPRESTAMO_ID'          => new sfValidatorPropelChoice(array('model' => 'DocPrestamo', 'column' => 'DOCPRESTAMO_ID')),
      'DOCSOLICITUDPRESTAMO_ID' => new sfValidatorPropelChoice(array('model' => 'DocSolicitudPrestamo', 'column' => 'DOCSOLICITUDPRESTAMO_ID')),
      'DOCESTADOPRESTAMO_ID'    => new sfValidatorPropelChoice(array('model' => 'DocEstadoPrestamo', 'column' => 'DOCESTADOPRESTAMO_ID')),
    ));

    $this->widgetSchema->setNameFormat('doc_detalle_prestamo[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'DocDetallePrestamo';
  }


}
