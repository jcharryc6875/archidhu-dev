<?php

/**
 * TipoContrato form base class.
 *
 * @method TipoContrato getObject() Returns the current form's model object
 *
 * @package    ##PROJECT_NAME##
 * @subpackage form
 * @author     ##AUTHOR_NAME##
 */
abstract class BaseTipoContratoForm extends BaseFormPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'TIPOCONTRATO_ID' => new sfWidgetFormInputHidden(),
      'DESCRIPCION'     => new sfWidgetFormInputText(),
      'ES_VISIBLE'      => new sfWidgetFormInputText(),
    ));

    $this->setValidators(array(
      'TIPOCONTRATO_ID' => new sfValidatorChoice(array('choices' => array($this->getObject()->getTipocontratoId()), 'empty_value' => $this->getObject()->getTipocontratoId(), 'required' => false)),
      'DESCRIPCION'     => new sfValidatorString(array('max_length' => 50, 'required' => false)),
      'ES_VISIBLE'      => new sfValidatorInteger(array('min' => -128, 'max' => 127, 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('tipo_contrato[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'TipoContrato';
  }


}
