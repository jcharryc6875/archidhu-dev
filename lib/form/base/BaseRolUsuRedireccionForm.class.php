<?php

/**
 * RolUsuRedireccion form base class.
 *
 * @method RolUsuRedireccion getObject() Returns the current form's model object
 *
 * @package    ##PROJECT_NAME##
 * @subpackage form
 * @author     ##AUTHOR_NAME##
 */
abstract class BaseRolUsuRedireccionForm extends BaseFormPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'ROLUSUREDIRECCION_ID' => new sfWidgetFormInputHidden(),
      'DESCRIPCION'          => new sfWidgetFormInputText(),
    ));

    $this->setValidators(array(
      'ROLUSUREDIRECCION_ID' => new sfValidatorChoice(array('choices' => array($this->getObject()->getRolusuredireccionId()), 'empty_value' => $this->getObject()->getRolusuredireccionId(), 'required' => false)),
      'DESCRIPCION'          => new sfValidatorString(array('max_length' => 250, 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('rol_usu_redireccion[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'RolUsuRedireccion';
  }


}
