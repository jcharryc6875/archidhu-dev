<?php

/**
 * EstadoUsuario form base class.
 *
 * @method EstadoUsuario getObject() Returns the current form's model object
 *
 * @package    ##PROJECT_NAME##
 * @subpackage form
 * @author     ##AUTHOR_NAME##
 */
abstract class BaseEstadoUsuarioForm extends BaseFormPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'ESTADOUSUARIO_ID' => new sfWidgetFormInputHidden(),
      'DESCRIPCION'      => new sfWidgetFormInputText(),
    ));

    $this->setValidators(array(
      'ESTADOUSUARIO_ID' => new sfValidatorChoice(array('choices' => array($this->getObject()->getEstadousuarioId()), 'empty_value' => $this->getObject()->getEstadousuarioId(), 'required' => false)),
      'DESCRIPCION'      => new sfValidatorString(array('max_length' => 250, 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('estado_usuario[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'EstadoUsuario';
  }


}
