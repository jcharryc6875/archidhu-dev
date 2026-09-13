<?php

/**
 * GrupoUsuario form base class.
 *
 * @method GrupoUsuario getObject() Returns the current form's model object
 *
 * @package    ##PROJECT_NAME##
 * @subpackage form
 * @author     ##AUTHOR_NAME##
 */
abstract class BaseGrupoUsuarioForm extends BaseFormPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'GRUPOUSUARI_ID' => new sfWidgetFormInputHidden(),
      'DESCRIPCION'    => new sfWidgetFormInputText(),
    ));

    $this->setValidators(array(
      'GRUPOUSUARI_ID' => new sfValidatorChoice(array('choices' => array($this->getObject()->getGrupousuariId()), 'empty_value' => $this->getObject()->getGrupousuariId(), 'required' => false)),
      'DESCRIPCION'    => new sfValidatorString(array('max_length' => 250, 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('grupo_usuario[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'GrupoUsuario';
  }


}
