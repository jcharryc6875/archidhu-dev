<?php

/**
 * Transferencia form base class.
 *
 * @method Transferencia getObject() Returns the current form's model object
 *
 * @package    ##PROJECT_NAME##
 * @subpackage form
 * @author     ##AUTHOR_NAME##
 */
abstract class BaseTransferenciaForm extends BaseFormPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'TRANSFERENCIA_ID'            => new sfWidgetFormInputHidden(),
      'VINCULADA_ID'                => new sfWidgetFormPropelChoice(array('model' => 'Vinculada', 'add_empty' => true)),
      'ESTADOTRANSFERENCIA_ID'      => new sfWidgetFormPropelChoice(array('model' => 'EstadoTransferencia', 'add_empty' => false)),
      'UNIDADDOCUMENTAL_ID'         => new sfWidgetFormPropelChoice(array('model' => 'UnidadDocumental', 'add_empty' => true)),
      'COMENVIADA_ID'               => new sfWidgetFormPropelChoice(array('model' => 'ComEnviada', 'add_empty' => true)),
      'COMINTERNA_ID'               => new sfWidgetFormPropelChoice(array('model' => 'ComInterna', 'add_empty' => true)),
      'ORIGENTRANSFERENCIAID'       => new sfWidgetFormPropelChoice(array('model' => 'OrigenTransferencia', 'add_empty' => false)),
      'COMRECIBIDA_ID'              => new sfWidgetFormPropelChoice(array('model' => 'ComRecibida', 'add_empty' => true)),
      'FACTURA_ID'                  => new sfWidgetFormPropelChoice(array('model' => 'Factura', 'add_empty' => true)),
      'DOCUMENTACION_ID'            => new sfWidgetFormPropelChoice(array('model' => 'Documentacion', 'add_empty' => true)),
      'TIPODOCUMENTAL_ID'           => new sfWidgetFormPropelChoice(array('model' => 'TipoDocumental', 'add_empty' => true)),
      'DESTINOTRANSFERENCIA_ID'     => new sfWidgetFormPropelChoice(array('model' => 'DestinoTransferencia', 'add_empty' => false)),
      'EMAIL_ID'                    => new sfWidgetFormPropelChoice(array('model' => 'Email', 'add_empty' => true)),
      'FECHA_CREACION'              => new sfWidgetFormDateTime(),
      'FECHA_ACEPTACION'            => new sfWidgetFormDateTime(),
      'OBSERVACIONES'               => new sfWidgetFormInputText(),
      'temp_usuariotranferencia_id' => new sfWidgetFormInputText(),
      'MARCA'                       => new sfWidgetFormInputText(),
    ));

    $this->setValidators(array(
      'TRANSFERENCIA_ID'            => new sfValidatorChoice(array('choices' => array($this->getObject()->getTransferenciaId()), 'empty_value' => $this->getObject()->getTransferenciaId(), 'required' => false)),
      'VINCULADA_ID'                => new sfValidatorPropelChoice(array('model' => 'Vinculada', 'column' => 'VINCULADA_ID', 'required' => false)),
      'ESTADOTRANSFERENCIA_ID'      => new sfValidatorPropelChoice(array('model' => 'EstadoTransferencia', 'column' => 'ESTADOTRANSFERENCIA_ID')),
      'UNIDADDOCUMENTAL_ID'         => new sfValidatorPropelChoice(array('model' => 'UnidadDocumental', 'column' => 'UNIDADDOCUMENTAL_ID', 'required' => false)),
      'COMENVIADA_ID'               => new sfValidatorPropelChoice(array('model' => 'ComEnviada', 'column' => 'COMENVIADA_ID', 'required' => false)),
      'COMINTERNA_ID'               => new sfValidatorPropelChoice(array('model' => 'ComInterna', 'column' => 'COMINTERNA_ID', 'required' => false)),
      'ORIGENTRANSFERENCIAID'       => new sfValidatorPropelChoice(array('model' => 'OrigenTransferencia', 'column' => 'ORIGENTRANSFERENCIAID')),
      'COMRECIBIDA_ID'              => new sfValidatorPropelChoice(array('model' => 'ComRecibida', 'column' => 'COMRECIBIDA_ID', 'required' => false)),
      'FACTURA_ID'                  => new sfValidatorPropelChoice(array('model' => 'Factura', 'column' => 'FACTURA_ID', 'required' => false)),
      'DOCUMENTACION_ID'            => new sfValidatorPropelChoice(array('model' => 'Documentacion', 'column' => 'DOCUMENTACION_ID', 'required' => false)),
      'TIPODOCUMENTAL_ID'           => new sfValidatorPropelChoice(array('model' => 'TipoDocumental', 'column' => 'TIPODOCUMENTAL_ID', 'required' => false)),
      'DESTINOTRANSFERENCIA_ID'     => new sfValidatorPropelChoice(array('model' => 'DestinoTransferencia', 'column' => 'DESTINOTRANSFERENCIA_ID')),
      'EMAIL_ID'                    => new sfValidatorPropelChoice(array('model' => 'Email', 'column' => 'EMAIL_ID', 'required' => false)),
      'FECHA_CREACION'              => new sfValidatorDateTime(array('required' => false)),
      'FECHA_ACEPTACION'            => new sfValidatorDateTime(array('required' => false)),
      'OBSERVACIONES'               => new sfValidatorString(array('max_length' => 200, 'required' => false)),
      'temp_usuariotranferencia_id' => new sfValidatorInteger(array('min' => -2147483648, 'max' => 2147483647, 'required' => false)),
      'MARCA'                       => new sfValidatorInteger(array('min' => -2147483648, 'max' => 2147483647, 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('transferencia[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'Transferencia';
  }


}
