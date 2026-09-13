<?php

/**
 * DirectorioBiblioteca form base class.
 *
 * @method DirectorioBiblioteca getObject() Returns the current form's model object
 *
 * @package    ##PROJECT_NAME##
 * @subpackage form
 * @author     ##AUTHOR_NAME##
 */
abstract class BaseDirectorioBibliotecaForm extends BaseFormPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'DIRECTORIOBIBLIOTECA_ID' => new sfWidgetFormInputHidden(),
      'DESCRIPCION'             => new sfWidgetFormInputText(),
      'URL'                     => new sfWidgetFormInputText(),
    ));

    $this->setValidators(array(
      'DIRECTORIOBIBLIOTECA_ID' => new sfValidatorChoice(array('choices' => array($this->getObject()->getDirectoriobibliotecaId()), 'empty_value' => $this->getObject()->getDirectoriobibliotecaId(), 'required' => false)),
      'DESCRIPCION'             => new sfValidatorString(array('max_length' => 250, 'required' => false)),
      'URL'                     => new sfValidatorString(array('max_length' => 200, 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('directorio_biblioteca[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'DirectorioBiblioteca';
  }


}
