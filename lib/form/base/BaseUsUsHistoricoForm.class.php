<?php

/**
 * UsUsHistorico form base class.
 *
 * @method UsUsHistorico getObject() Returns the current form's model object
 *
 * @package    ##PROJECT_NAME##
 * @subpackage form
 * @author     ##AUTHOR_NAME##
 */
abstract class BaseUsUsHistoricoForm extends BaseFormPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'USUSHISTORICO_ID'  => new sfWidgetFormInputHidden(),
      'USUARIO_ID'        => new sfWidgetFormPropelChoice(array('model' => 'Usuario', 'add_empty' => false)),
      'HISTUSUARIO_ID'    => new sfWidgetFormPropelChoice(array('model' => 'HistUsuario', 'add_empty' => false)),
      'ROLUSHISTORICO_ID' => new sfWidgetFormPropelChoice(array('model' => 'RolUsHistorico', 'add_empty' => false)),
    ));

    $this->setValidators(array(
      'USUSHISTORICO_ID'  => new sfValidatorChoice(array('choices' => array($this->getObject()->getUsushistoricoId()), 'empty_value' => $this->getObject()->getUsushistoricoId(), 'required' => false)),
      'USUARIO_ID'        => new sfValidatorPropelChoice(array('model' => 'Usuario', 'column' => 'USUARIO_ID')),
      'HISTUSUARIO_ID'    => new sfValidatorPropelChoice(array('model' => 'HistUsuario', 'column' => 'HISTUSUARIO_ID')),
      'ROLUSHISTORICO_ID' => new sfValidatorPropelChoice(array('model' => 'RolUsHistorico', 'column' => 'ROLUSHISTORICO_ID')),
    ));

    $this->widgetSchema->setNameFormat('us_us_historico[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'UsUsHistorico';
  }


}
