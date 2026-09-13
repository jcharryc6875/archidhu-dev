<?php

/**
 * TipoPeticionario form base class.
 *
 * @method TipoPeticionario getObject() Returns the current form's model object
 *
 * @package    ##PROJECT_NAME##
 * @subpackage form
 * @author     ##AUTHOR_NAME##
 */
abstract class BaseTipoPeticionarioForm extends BaseFormPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'TIPOPETICIONARIO_ID' => new sfWidgetFormInputHidden(),
      'DESCRIPCION'         => new sfWidgetFormInputText(),
      'ES_VISIBLE'          => new sfWidgetFormInputText(),
    ));

    $this->setValidators(array(
      'TIPOPETICIONARIO_ID' => new sfValidatorChoice(array('choices' => array($this->getObject()->getTipopeticionarioId()), 'empty_value' => $this->getObject()->getTipopeticionarioId(), 'required' => false)),
      'DESCRIPCION'         => new sfValidatorString(array('max_length' => 50, 'required' => false)),
      'ES_VISIBLE'          => new sfValidatorInteger(array('min' => -128, 'max' => 127, 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('tipo_peticionario[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'TipoPeticionario';
  }


}
