<?php

/**
 * Parametro form base class.
 *
 * @method Parametro getObject() Returns the current form's model object
 *
 * @package    ##PROJECT_NAME##
 * @subpackage form
 * @author     ##AUTHOR_NAME##
 */
abstract class BaseParametroForm extends BaseFormPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'PARAMETRO_ID'   => new sfWidgetFormInputHidden(),
      'CODIGO'         => new sfWidgetFormInputText(),
      'DESCRIPCION'    => new sfWidgetFormInputText(),
      'VALORTEXTO'     => new sfWidgetFormInputText(),
      'VALOR_NUMERICO' => new sfWidgetFormInputText(),
    ));

    $this->setValidators(array(
      'PARAMETRO_ID'   => new sfValidatorChoice(array('choices' => array($this->getObject()->getParametroId()), 'empty_value' => $this->getObject()->getParametroId(), 'required' => false)),
      'CODIGO'         => new sfValidatorString(array('max_length' => 50, 'required' => false)),
      'DESCRIPCION'    => new sfValidatorString(array('max_length' => 250, 'required' => false)),
      'VALORTEXTO'     => new sfValidatorString(array('max_length' => 200, 'required' => false)),
      'VALOR_NUMERICO' => new sfValidatorInteger(array('min' => -2147483648, 'max' => 2147483647, 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('parametro[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'Parametro';
  }


}
