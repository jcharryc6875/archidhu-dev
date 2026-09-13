<?php

/**
 * ProvEstadoSolMod form base class.
 *
 * @method ProvEstadoSolMod getObject() Returns the current form's model object
 *
 * @package    ##PROJECT_NAME##
 * @subpackage form
 * @author     ##AUTHOR_NAME##
 */
abstract class BaseProvEstadoSolModForm extends BaseFormPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'PROV_ESTADO_SOL_MOD_ID' => new sfWidgetFormInputHidden(),
      'DESCRIPCION'            => new sfWidgetFormInputText(),
    ));

    $this->setValidators(array(
      'PROV_ESTADO_SOL_MOD_ID' => new sfValidatorChoice(array('choices' => array($this->getObject()->getProvEstadoSolModId()), 'empty_value' => $this->getObject()->getProvEstadoSolModId(), 'required' => false)),
      'DESCRIPCION'            => new sfValidatorString(array('max_length' => 250, 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('prov_estado_sol_mod[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'ProvEstadoSolMod';
  }


}
