<?php

/**
 * PermisoUsuarioUniddoc form base class.
 *
 * @method PermisoUsuarioUniddoc getObject() Returns the current form's model object
 *
 * @package    ##PROJECT_NAME##
 * @subpackage form
 * @author     ##AUTHOR_NAME##
 */
abstract class BasePermisoUsuarioUniddocForm extends BaseFormPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'PERMISOUSUARIOUNIDDOC_ID' => new sfWidgetFormInputHidden(),
      'UNIDADDOCUMENTAL_ID'      => new sfWidgetFormPropelChoice(array('model' => 'UnidadDocumental', 'add_empty' => false)),
      'USUARIO_ID'               => new sfWidgetFormPropelChoice(array('model' => 'Usuario', 'add_empty' => false)),
    ));

    $this->setValidators(array(
      'PERMISOUSUARIOUNIDDOC_ID' => new sfValidatorChoice(array('choices' => array($this->getObject()->getPermisousuariouniddocId()), 'empty_value' => $this->getObject()->getPermisousuariouniddocId(), 'required' => false)),
      'UNIDADDOCUMENTAL_ID'      => new sfValidatorPropelChoice(array('model' => 'UnidadDocumental', 'column' => 'UNIDADDOCUMENTAL_ID')),
      'USUARIO_ID'               => new sfValidatorPropelChoice(array('model' => 'Usuario', 'column' => 'USUARIO_ID')),
    ));

    $this->widgetSchema->setNameFormat('permiso_usuario_uniddoc[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'PermisoUsuarioUniddoc';
  }


}
