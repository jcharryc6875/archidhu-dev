<?php

/**
 * UnidaddocumentalUsuario form base class.
 *
 * @method UnidaddocumentalUsuario getObject() Returns the current form's model object
 *
 * @package    ##PROJECT_NAME##
 * @subpackage form
 * @author     ##AUTHOR_NAME##
 */
abstract class BaseUnidaddocumentalUsuarioForm extends BaseFormPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'UNIDADDOCUMENTALUSUARIO_ID' => new sfWidgetFormInputHidden(),
      'ROLUSUUNIDADDOC_ID'         => new sfWidgetFormPropelChoice(array('model' => 'RolUsuUnidadDoc', 'add_empty' => false)),
      'USUARIO_ID'                 => new sfWidgetFormPropelChoice(array('model' => 'Usuario', 'add_empty' => false)),
      'UNIDADDOCUMENTAL_ID'        => new sfWidgetFormPropelChoice(array('model' => 'UnidadDocumental', 'add_empty' => false)),
    ));

    $this->setValidators(array(
      'UNIDADDOCUMENTALUSUARIO_ID' => new sfValidatorChoice(array('choices' => array($this->getObject()->getUnidaddocumentalusuarioId()), 'empty_value' => $this->getObject()->getUnidaddocumentalusuarioId(), 'required' => false)),
      'ROLUSUUNIDADDOC_ID'         => new sfValidatorPropelChoice(array('model' => 'RolUsuUnidadDoc', 'column' => 'ROLUSUUNIDADDOC_ID')),
      'USUARIO_ID'                 => new sfValidatorPropelChoice(array('model' => 'Usuario', 'column' => 'USUARIO_ID')),
      'UNIDADDOCUMENTAL_ID'        => new sfValidatorPropelChoice(array('model' => 'UnidadDocumental', 'column' => 'UNIDADDOCUMENTAL_ID')),
    ));

    $this->widgetSchema->setNameFormat('unidaddocumental_usuario[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'UnidaddocumentalUsuario';
  }


}
