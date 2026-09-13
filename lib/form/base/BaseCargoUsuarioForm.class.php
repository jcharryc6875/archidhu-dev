<?php

/**
 * CargoUsuario form base class.
 *
 * @method CargoUsuario getObject() Returns the current form's model object
 *
 * @package    ##PROJECT_NAME##
 * @subpackage form
 * @author     ##AUTHOR_NAME##
 */
abstract class BaseCargoUsuarioForm extends BaseFormPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'CARGOUSUARIO_ID' => new sfWidgetFormInputHidden(),
      'CARGO_ID'        => new sfWidgetFormPropelChoice(array('model' => 'Cargo', 'add_empty' => true)),
      'USUARIO_ID'      => new sfWidgetFormPropelChoice(array('model' => 'Usuario', 'add_empty' => false)),
      'FECHA_INICIO'    => new sfWidgetFormDateTime(),
      'FECHA_FIN'       => new sfWidgetFormDateTime(),
      'FECHA_CREACION'  => new sfWidgetFormDateTime(),
      'ES_ACTUAL'       => new sfWidgetFormInputText(),
      'ES_PRINCIPAL'    => new sfWidgetFormInputText(),
    ));

    $this->setValidators(array(
      'CARGOUSUARIO_ID' => new sfValidatorChoice(array('choices' => array($this->getObject()->getCargousuarioId()), 'empty_value' => $this->getObject()->getCargousuarioId(), 'required' => false)),
      'CARGO_ID'        => new sfValidatorPropelChoice(array('model' => 'Cargo', 'column' => 'CARGO_ID', 'required' => false)),
      'USUARIO_ID'      => new sfValidatorPropelChoice(array('model' => 'Usuario', 'column' => 'USUARIO_ID')),
      'FECHA_INICIO'    => new sfValidatorDateTime(array('required' => false)),
      'FECHA_FIN'       => new sfValidatorDateTime(array('required' => false)),
      'FECHA_CREACION'  => new sfValidatorDateTime(array('required' => false)),
      'ES_ACTUAL'       => new sfValidatorInteger(array('min' => -128, 'max' => 127, 'required' => false)),
      'ES_PRINCIPAL'    => new sfValidatorInteger(array('min' => -128, 'max' => 127, 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('cargo_usuario[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'CargoUsuario';
  }


}
