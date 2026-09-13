<?php

/**
 * ComRadicarLote form base class.
 *
 * @package    form
 * @subpackage com_radicar_lote
 * @version    SVN: $Id: sfPropelFormGeneratedTemplate.php 15484 2009-02-13 13:13:51Z fabien $
 */
class BaseComRadicarLoteForm extends BaseFormPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'comradicarlote_id'   => new sfWidgetFormInputHidden(),
      'contenido'           => new sfWidgetFormInputText(),
      'asunto'              => new sfWidgetFormInputText(),
      'origenradicacion'    => new sfWidgetFormInputText(),
      'destinatario'        => new sfWidgetFormInputText(),
      'remitente'           => new sfWidgetFormInputText(),
      'fecha_creacion'      => new sfWidgetFormDateTime(),
      'fecha_radicacion'    => new sfWidgetFormDateTime(),
      'radicado'            => new sfWidgetFormInputText(),
      'estado_radicacion'   => new sfWidgetFormInputText(),
      'error_radicacion'    => new sfWidgetFormInputText(),
      'origen_id'           => new sfWidgetFormInputText(),
      'origen_estado_final' => new sfWidgetFormInputText(),
    ));

    $this->setValidators(array(
      'comradicarlote_id'   => new sfValidatorPropelChoice(array('model' => 'ComRadicarLote', 'column' => 'comradicarlote_id', 'required' => false)),
      'contenido'           => new sfValidatorString(array('max_length' => 2000, 'required' => false)),
      'asunto'              => new sfValidatorString(array('max_length' => 200, 'required' => false)),
      'origenradicacion'    => new sfValidatorString(array('max_length' => 20, 'required' => false)),
      'destinatario'        => new sfValidatorInteger(array('required' => false)),
      'remitente'           => new sfValidatorInteger(array('required' => false)),
      'fecha_creacion'      => new sfValidatorDateTime(array('required' => false)),
      'fecha_radicacion'    => new sfValidatorDateTime(array('required' => false)),
      'radicado'            => new sfValidatorString(array('max_length' => 20, 'required' => false)),
      'estado_radicacion'   => new sfValidatorInteger(array('required' => false)),
      'error_radicacion'    => new sfValidatorString(array('max_length' => 1000, 'required' => false)),
      'origen_id'           => new sfValidatorInteger(array('required' => false)),
      'origen_estado_final' => new sfValidatorString(array('max_length' => 10, 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('com_radicar_lote[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'ComRadicarLote';
  }


}
