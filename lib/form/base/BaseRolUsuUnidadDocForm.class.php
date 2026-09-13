<?php

/**
 * RolUsuUnidadDoc form base class.
 *
 * @method RolUsuUnidadDoc getObject() Returns the current form's model object
 *
 * @package    ##PROJECT_NAME##
 * @subpackage form
 * @author     ##AUTHOR_NAME##
 */
abstract class BaseRolUsuUnidadDocForm extends BaseFormPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'ROLUSUUNIDADDOC_ID' => new sfWidgetFormInputHidden(),
      'DESCRIPCION'        => new sfWidgetFormInputText(),
    ));

    $this->setValidators(array(
      'ROLUSUUNIDADDOC_ID' => new sfValidatorChoice(array('choices' => array($this->getObject()->getRolusuunidaddocId()), 'empty_value' => $this->getObject()->getRolusuunidaddocId(), 'required' => false)),
      'DESCRIPCION'        => new sfValidatorString(array('max_length' => 250, 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('rol_usu_unidad_doc[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'RolUsuUnidadDoc';
  }


}
