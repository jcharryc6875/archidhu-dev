<?php

/**
 * AsuntoRecibida form base class.
 *
 * @method AsuntoRecibida getObject() Returns the current form's model object
 *
 * @package    ##PROJECT_NAME##
 * @subpackage form
 * @author     ##AUTHOR_NAME##
 */
abstract class BaseAsuntoRecibidaForm extends BaseFormPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'ASUNTORECIBIDA_ID'  => new sfWidgetFormInputHidden(),
      'DESCRIPCION'        => new sfWidgetFormInputText(),
      'VENTANILLA_DEFAULT' => new sfWidgetFormInputText(),
    ));

    $this->setValidators(array(
      'ASUNTORECIBIDA_ID'  => new sfValidatorChoice(array('choices' => array($this->getObject()->getAsuntorecibidaId()), 'empty_value' => $this->getObject()->getAsuntorecibidaId(), 'required' => false)),
      'DESCRIPCION'        => new sfValidatorString(array('max_length' => 250, 'required' => false)),
      'VENTANILLA_DEFAULT' => new sfValidatorInteger(array('min' => -128, 'max' => 127, 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('asunto_recibida[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'AsuntoRecibida';
  }


}
