<?php

/**
 * Sysdiagrams form base class.
 *
 * @package    form
 * @subpackage sysdiagrams
 * @version    SVN: $Id: sfPropelFormGeneratedTemplate.php 15484 2009-02-13 13:13:51Z fabien $
 */
class BaseSysdiagramsForm extends BaseFormPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'name'         => new sfWidgetFormInputText(),
      'principal_id' => new sfWidgetFormInputText(),
      'diagram_id'   => new sfWidgetFormInputHidden(),
      'version'      => new sfWidgetFormInputText(),
      'definition'   => new sfWidgetFormInputText(),
    ));

    $this->setValidators(array(
      'name'         => new sfValidatorString(array('max_length' => 256)),
      'principal_id' => new sfValidatorInteger(),
      'diagram_id'   => new sfValidatorPropelChoice(array('model' => 'Sysdiagrams', 'column' => 'diagram_id', 'required' => false)),
      'version'      => new sfValidatorInteger(array('required' => false)),
      'definition'   => new sfValidatorPass(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('sysdiagrams[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'Sysdiagrams';
  }


}
