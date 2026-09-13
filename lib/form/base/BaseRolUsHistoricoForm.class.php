<?php

/**
 * RolUsHistorico form base class.
 *
 * @method RolUsHistorico getObject() Returns the current form's model object
 *
 * @package    ##PROJECT_NAME##
 * @subpackage form
 * @author     ##AUTHOR_NAME##
 */
abstract class BaseRolUsHistoricoForm extends BaseFormPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'ROLUSHISTORICO_ID' => new sfWidgetFormInputHidden(),
      'DESCRIPCION'       => new sfWidgetFormInputText(),
    ));

    $this->setValidators(array(
      'ROLUSHISTORICO_ID' => new sfValidatorChoice(array('choices' => array($this->getObject()->getRolushistoricoId()), 'empty_value' => $this->getObject()->getRolushistoricoId(), 'required' => false)),
      'DESCRIPCION'       => new sfValidatorString(array('max_length' => 250, 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('rol_us_historico[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'RolUsHistorico';
  }


}
