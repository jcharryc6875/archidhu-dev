<?php

/**
 * DirectorioExtended form base class.
 *
 * @method DirectorioExtended getObject() Returns the current form's model object
 *
 * @package    ##PROJECT_NAME##
 * @subpackage form
 * @author     ##AUTHOR_NAME##
 */
abstract class BaseDirectorioExtendedForm extends BaseFormPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'DIRECTORIOEXTENDED_ID' => new sfWidgetFormInputHidden(),
      'DIRECTORIOEXTERNO_ID'  => new sfWidgetFormPropelChoice(array('model' => 'DirectorioExterno', 'add_empty' => false)),
      'FUNCIONARIO'           => new sfWidgetFormInputText(),
      'CARGO'                 => new sfWidgetFormInputText(),
      'DIRECCION'             => new sfWidgetFormInputText(),
    ));

    $this->setValidators(array(
      'DIRECTORIOEXTENDED_ID' => new sfValidatorChoice(array('choices' => array($this->getObject()->getDirectorioextendedId()), 'empty_value' => $this->getObject()->getDirectorioextendedId(), 'required' => false)),
      'DIRECTORIOEXTERNO_ID'  => new sfValidatorPropelChoice(array('model' => 'DirectorioExterno', 'column' => 'DIRECTORIOEXTERNO_ID')),
      'FUNCIONARIO'           => new sfValidatorString(array('max_length' => 80, 'required' => false)),
      'CARGO'                 => new sfValidatorString(array('max_length' => 50, 'required' => false)),
      'DIRECCION'             => new sfValidatorString(array('max_length' => 200, 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('directorio_extended[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'DirectorioExtended';
  }


}
