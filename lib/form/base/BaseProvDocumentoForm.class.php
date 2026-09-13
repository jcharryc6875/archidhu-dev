<?php

/**
 * ProvDocumento form base class.
 *
 * @method ProvDocumento getObject() Returns the current form's model object
 *
 * @package    ##PROJECT_NAME##
 * @subpackage form
 * @author     ##AUTHOR_NAME##
 */
abstract class BaseProvDocumentoForm extends BaseFormPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'PROV_DOCUMENTO_ID'       => new sfWidgetFormInputHidden(),
      'PROV_PERIODO_VALIDEZ_ID' => new sfWidgetFormPropelChoice(array('model' => 'ProvPeriodoValidez', 'add_empty' => false)),
      'PROV_LISTA_DOCS_ID'      => new sfWidgetFormPropelChoice(array('model' => 'ProvListaDocs', 'add_empty' => false)),
      'PROV_ESTADO_DOC_ID'      => new sfWidgetFormPropelChoice(array('model' => 'ProvEstadoDoc', 'add_empty' => false)),
      'FECHA_CREACION'          => new sfWidgetFormDateTime(),
      'OBSERVACIONES'           => new sfWidgetFormInputText(),
      'RUTA'                    => new sfWidgetFormInputText(),
      'FECHA_RECIBIDO'          => new sfWidgetFormDateTime(),
    ));

    $this->setValidators(array(
      'PROV_DOCUMENTO_ID'       => new sfValidatorChoice(array('choices' => array($this->getObject()->getProvDocumentoId()), 'empty_value' => $this->getObject()->getProvDocumentoId(), 'required' => false)),
      'PROV_PERIODO_VALIDEZ_ID' => new sfValidatorPropelChoice(array('model' => 'ProvPeriodoValidez', 'column' => 'PROV_PERIODO_VALIDEZ_ID')),
      'PROV_LISTA_DOCS_ID'      => new sfValidatorPropelChoice(array('model' => 'ProvListaDocs', 'column' => 'PROV_LISTA_DOCS_ID')),
      'PROV_ESTADO_DOC_ID'      => new sfValidatorPropelChoice(array('model' => 'ProvEstadoDoc', 'column' => 'PROV_ESTADO_DOC_ID')),
      'FECHA_CREACION'          => new sfValidatorDateTime(array('required' => false)),
      'OBSERVACIONES'           => new sfValidatorString(array('max_length' => 200, 'required' => false)),
      'RUTA'                    => new sfValidatorString(array('max_length' => 1000, 'required' => false)),
      'FECHA_RECIBIDO'          => new sfValidatorDateTime(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('prov_documento[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'ProvDocumento';
  }


}
