<?php

/**
 * EmpresaMensajeria form base class.
 *
 * @method EmpresaMensajeria getObject() Returns the current form's model object
 *
 * @package    ##PROJECT_NAME##
 * @subpackage form
 * @author     ##AUTHOR_NAME##
 */
abstract class BaseEmpresaMensajeriaForm extends BaseFormPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'EMPRESA_MENSAJERIA_ID' => new sfWidgetFormInputHidden(),
      'NOMBRE'                => new sfWidgetFormInputText(),
      'URL'                   => new sfWidgetFormInputText(),
      'ABREVIATURA'           => new sfWidgetFormInputText(),
    ));

    $this->setValidators(array(
      'EMPRESA_MENSAJERIA_ID' => new sfValidatorChoice(array('choices' => array($this->getObject()->getEmpresaMensajeriaId()), 'empty_value' => $this->getObject()->getEmpresaMensajeriaId(), 'required' => false)),
      'NOMBRE'                => new sfValidatorString(array('max_length' => 100, 'required' => false)),
      'URL'                   => new sfValidatorString(array('max_length' => 200, 'required' => false)),
      'ABREVIATURA'           => new sfValidatorString(array('max_length' => 20, 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('empresa_mensajeria[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'EmpresaMensajeria';
  }


}
