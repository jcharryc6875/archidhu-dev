<?php

/**
 * ManualFormas form base class.
 *
 * @method ManualFormas getObject() Returns the current form's model object
 *
 * @package    ##PROJECT_NAME##
 * @subpackage form
 * @author     ##AUTHOR_NAME##
 */
abstract class BaseManualFormasForm extends BaseFormPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'MANUALFORMAS_ID'    => new sfWidgetFormInputHidden(),
      'TIPOMANUALFORMA_ID' => new sfWidgetFormPropelChoice(array('model' => 'TipoManualForma', 'add_empty' => true)),
    ));

    $this->setValidators(array(
      'MANUALFORMAS_ID'    => new sfValidatorChoice(array('choices' => array($this->getObject()->getManualformasId()), 'empty_value' => $this->getObject()->getManualformasId(), 'required' => false)),
      'TIPOMANUALFORMA_ID' => new sfValidatorPropelChoice(array('model' => 'TipoManualForma', 'column' => 'TIPOMANUALFORMA_ID', 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('manual_formas[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'ManualFormas';
  }


}
