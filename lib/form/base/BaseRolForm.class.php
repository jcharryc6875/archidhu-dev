<?php

/**
 * Rol form base class.
 *
 * @method Rol getObject() Returns the current form's model object
 *
 * @package    ##PROJECT_NAME##
 * @subpackage form
 * @author     ##AUTHOR_NAME##
 */
abstract class BaseRolForm extends BaseFormPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'ROL_ID'      => new sfWidgetFormInputHidden(),
      'DESCRIPCION' => new sfWidgetFormInputText(),
      'ENTIDAD_ID'  => new sfWidgetFormPropelChoice(array('model' => 'Entidad', 'add_empty' => true)),
    ));

    $this->setValidators(array(
      'ROL_ID'      => new sfValidatorChoice(array('choices' => array($this->getObject()->getRolId()), 'empty_value' => $this->getObject()->getRolId(), 'required' => false)),
      'DESCRIPCION' => new sfValidatorString(array('max_length' => 250, 'required' => false)),
      'ENTIDAD_ID'  => new sfValidatorPropelChoice(array('model' => 'Entidad', 'column' => 'ENTIDAD_ID', 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('rol[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'Rol';
  }


}
