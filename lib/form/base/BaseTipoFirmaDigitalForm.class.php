<?php

/**
 * TipoFirmaDigital form base class.
 *
 * @method TipoFirmaDigital getObject() Returns the current form's model object
 *
 * @package    ##PROJECT_NAME##
 * @subpackage form
 * @author     ##AUTHOR_NAME##
 */
abstract class BaseTipoFirmaDigitalForm extends BaseFormPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'TIPOFIRMADIGITAL_ID' => new sfWidgetFormInputHidden(),
      'DESCRIPCION'         => new sfWidgetFormInputText(),
    ));

    $this->setValidators(array(
      'TIPOFIRMADIGITAL_ID' => new sfValidatorChoice(array('choices' => array($this->getObject()->getTipofirmadigitalId()), 'empty_value' => $this->getObject()->getTipofirmadigitalId(), 'required' => false)),
      'DESCRIPCION'         => new sfValidatorString(array('max_length' => 50, 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('tipo_firma_digital[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'TipoFirmaDigital';
  }


}
