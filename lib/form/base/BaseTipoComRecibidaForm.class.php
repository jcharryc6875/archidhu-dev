<?php

/**
 * TipoComRecibida form base class.
 *
 * @method TipoComRecibida getObject() Returns the current form's model object
 *
 * @package    ##PROJECT_NAME##
 * @subpackage form
 * @author     ##AUTHOR_NAME##
 */
abstract class BaseTipoComRecibidaForm extends BaseFormPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'TIPOCOMRECIBIDA_ID'  => new sfWidgetFormInputHidden(),
      'DESCRIPCION'         => new sfWidgetFormInputText(),
      'DIAS_RESPUESTA'      => new sfWidgetFormInputText(),
      'ES_ACCION_LEGAL'     => new sfWidgetFormInputText(),
      'TIPOFIRMADIGITAL_ID' => new sfWidgetFormInputText(),
      'TIPODISTRIBUCION_ID' => new sfWidgetFormPropelChoice(array('model' => 'TipoDistribucion', 'add_empty' => true)),
      'ES_VISIBLE'          => new sfWidgetFormInputText(),
      'DIAS_HABILES'        => new sfWidgetFormInputText(),
    ));

    $this->setValidators(array(
      'TIPOCOMRECIBIDA_ID'  => new sfValidatorChoice(array('choices' => array($this->getObject()->getTipocomrecibidaId()), 'empty_value' => $this->getObject()->getTipocomrecibidaId(), 'required' => false)),
      'DESCRIPCION'         => new sfValidatorString(array('max_length' => 250, 'required' => false)),
      'DIAS_RESPUESTA'      => new sfValidatorInteger(array('min' => -32768, 'max' => 32767, 'required' => false)),
      'ES_ACCION_LEGAL'     => new sfValidatorInteger(array('min' => -128, 'max' => 127, 'required' => false)),
      'TIPOFIRMADIGITAL_ID' => new sfValidatorInteger(array('min' => -2147483648, 'max' => 2147483647, 'required' => false)),
      'TIPODISTRIBUCION_ID' => new sfValidatorPropelChoice(array('model' => 'TipoDistribucion', 'column' => 'TIPODISTRIBUCION_ID', 'required' => false)),
      'ES_VISIBLE'          => new sfValidatorInteger(array('min' => -128, 'max' => 127, 'required' => false)),
      'DIAS_HABILES'        => new sfValidatorInteger(array('min' => -2147483648, 'max' => 2147483647, 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('tipo_com_recibida[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'TipoComRecibida';
  }


}
