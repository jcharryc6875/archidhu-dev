<?php

/**
 * TipoManualForma form base class.
 *
 * @method TipoManualForma getObject() Returns the current form's model object
 *
 * @package    ##PROJECT_NAME##
 * @subpackage form
 * @author     ##AUTHOR_NAME##
 */
abstract class BaseTipoManualFormaForm extends BaseFormPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'TIPOMANUALFORMA_ID' => new sfWidgetFormInputHidden(),
    ));

    $this->setValidators(array(
      'TIPOMANUALFORMA_ID' => new sfValidatorChoice(array('choices' => array($this->getObject()->getTipomanualformaId()), 'empty_value' => $this->getObject()->getTipomanualformaId(), 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('tipo_manual_forma[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'TipoManualForma';
  }


}
