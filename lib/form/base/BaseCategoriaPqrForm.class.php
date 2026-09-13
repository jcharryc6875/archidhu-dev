<?php

/**
 * CategoriaPqr form base class.
 *
 * @method CategoriaPqr getObject() Returns the current form's model object
 *
 * @package    ##PROJECT_NAME##
 * @subpackage form
 * @author     ##AUTHOR_NAME##
 */
abstract class BaseCategoriaPqrForm extends BaseFormPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'CATEGORIAPQR_ID' => new sfWidgetFormInputHidden(),
      'DESCRIPCION'     => new sfWidgetFormInputText(),
    ));

    $this->setValidators(array(
      'CATEGORIAPQR_ID' => new sfValidatorChoice(array('choices' => array($this->getObject()->getCategoriapqrId()), 'empty_value' => $this->getObject()->getCategoriapqrId(), 'required' => false)),
      'DESCRIPCION'     => new sfValidatorString(array('max_length' => 250, 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('categoria_pqr[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'CategoriaPqr';
  }


}
