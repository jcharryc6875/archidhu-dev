<?php

/**
 * ClTransferencia form base class.
 *
 * @method ClTransferencia getObject() Returns the current form's model object
 *
 * @package    ##PROJECT_NAME##
 * @subpackage form
 * @author     ##AUTHOR_NAME##
 */
abstract class BaseClTransferenciaForm extends BaseFormPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'CLTRANSFERENCIA_ID'        => new sfWidgetFormInputHidden(),
      'CLESTADOTRANSFERENCIA_ID'  => new sfWidgetFormPropelChoice(array('model' => 'ClEstadoTransferencia', 'add_empty' => false)),
      'COMINTERNA_ID'             => new sfWidgetFormPropelChoice(array('model' => 'ComInterna', 'add_empty' => true)),
      'COMRECIBIDA_ID'            => new sfWidgetFormPropelChoice(array('model' => 'ComRecibida', 'add_empty' => true)),
      'CLIENTE_ID'                => new sfWidgetFormPropelChoice(array('model' => 'Cliente', 'add_empty' => true)),
      'COMENVIADA_ID'             => new sfWidgetFormPropelChoice(array('model' => 'ComEnviada', 'add_empty' => true)),
      'CLORIGENTRANSFERENCIA_ID'  => new sfWidgetFormPropelChoice(array('model' => 'ClOrigenTransferencia', 'add_empty' => false)),
      'CLDESTINOTRANSFERENCIA_ID' => new sfWidgetFormPropelChoice(array('model' => 'ClDestinoTransferencia', 'add_empty' => false)),
      'TIPODOCUMENTAL_ID'         => new sfWidgetFormPropelChoice(array('model' => 'TipoDocumental', 'add_empty' => true)),
      'DOCUMENTACION_ID'          => new sfWidgetFormPropelChoice(array('model' => 'Documentacion', 'add_empty' => true)),
      'FECHA_ACEPTACION'          => new sfWidgetFormDateTime(),
      'FECHA_CREACION'            => new sfWidgetFormDateTime(),
    ));

    $this->setValidators(array(
      'CLTRANSFERENCIA_ID'        => new sfValidatorChoice(array('choices' => array($this->getObject()->getCltransferenciaId()), 'empty_value' => $this->getObject()->getCltransferenciaId(), 'required' => false)),
      'CLESTADOTRANSFERENCIA_ID'  => new sfValidatorPropelChoice(array('model' => 'ClEstadoTransferencia', 'column' => 'CLESTADOTRANSFERENCIA_ID')),
      'COMINTERNA_ID'             => new sfValidatorPropelChoice(array('model' => 'ComInterna', 'column' => 'COMINTERNA_ID', 'required' => false)),
      'COMRECIBIDA_ID'            => new sfValidatorPropelChoice(array('model' => 'ComRecibida', 'column' => 'COMRECIBIDA_ID', 'required' => false)),
      'CLIENTE_ID'                => new sfValidatorPropelChoice(array('model' => 'Cliente', 'column' => 'CLIENTE_ID', 'required' => false)),
      'COMENVIADA_ID'             => new sfValidatorPropelChoice(array('model' => 'ComEnviada', 'column' => 'COMENVIADA_ID', 'required' => false)),
      'CLORIGENTRANSFERENCIA_ID'  => new sfValidatorPropelChoice(array('model' => 'ClOrigenTransferencia', 'column' => 'CLORIGENTRANSFERENCIA_ID')),
      'CLDESTINOTRANSFERENCIA_ID' => new sfValidatorPropelChoice(array('model' => 'ClDestinoTransferencia', 'column' => 'CLDESTINOTRANSFERENCIA_ID')),
      'TIPODOCUMENTAL_ID'         => new sfValidatorPropelChoice(array('model' => 'TipoDocumental', 'column' => 'TIPODOCUMENTAL_ID', 'required' => false)),
      'DOCUMENTACION_ID'          => new sfValidatorPropelChoice(array('model' => 'Documentacion', 'column' => 'DOCUMENTACION_ID', 'required' => false)),
      'FECHA_ACEPTACION'          => new sfValidatorDateTime(array('required' => false)),
      'FECHA_CREACION'            => new sfValidatorDateTime(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('cl_transferencia[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'ClTransferencia';
  }


}
