<?php

/**
 * CategoriaEscala form base class.
 *
 * @method CategoriaEscala getObject() Returns the current form's model object
 *
 * @package    ##PROJECT_NAME##
 * @subpackage form
 * @author     ##AUTHOR_NAME##
 */
abstract class BaseCategoriaEscalaForm extends BaseFormPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'CATEGORIAESCALA_ID' => new sfWidgetFormInputHidden(),
      'DESCRIPCION'        => new sfWidgetFormInputText(),
    ));

    $this->setValidators(array(
      'CATEGORIAESCALA_ID' => new sfValidatorChoice(array('choices' => array($this->getObject()->getCategoriaescalaId()), 'empty_value' => $this->getObject()->getCategoriaescalaId(), 'required' => false)),
      'DESCRIPCION'        => new sfValidatorString(array('max_length' => 250, 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('categoria_escala[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'CategoriaEscala';
  }


}
