<?php

/**
 * Receptor form base class.
 *
 * @method Receptor getObject() Returns the current form's model object
 *
 * @package    ##PROJECT_NAME##
 * @subpackage form
 * @author     ##AUTHOR_NAME##
 */
abstract class BaseReceptorForm extends BaseFormPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'RECEPTOR_ID'    => new sfWidgetFormInputHidden(),
      'MODULO_ID'      => new sfWidgetFormPropelChoice(array('model' => 'Modulo', 'add_empty' => false)),
      'REGIONAL_ID'    => new sfWidgetFormPropelChoice(array('model' => 'Regional', 'add_empty' => true)),
      'USUARIO_ID'     => new sfWidgetFormPropelChoice(array('model' => 'Usuario', 'add_empty' => false)),
      'DEPENDENCIA_ID' => new sfWidgetFormPropelChoice(array('model' => 'Dependencia', 'add_empty' => false)),
      'ESTA_ACTIVO'    => new sfWidgetFormInputText(),
    ));

    $this->setValidators(array(
      'RECEPTOR_ID'    => new sfValidatorChoice(array('choices' => array($this->getObject()->getReceptorId()), 'empty_value' => $this->getObject()->getReceptorId(), 'required' => false)),
      'MODULO_ID'      => new sfValidatorPropelChoice(array('model' => 'Modulo', 'column' => 'MODULO_ID')),
      'REGIONAL_ID'    => new sfValidatorPropelChoice(array('model' => 'Regional', 'column' => 'REGIONAL_ID', 'required' => false)),
      'USUARIO_ID'     => new sfValidatorPropelChoice(array('model' => 'Usuario', 'column' => 'USUARIO_ID')),
      'DEPENDENCIA_ID' => new sfValidatorPropelChoice(array('model' => 'Dependencia', 'column' => 'DEPENDENCIA_ID')),
      'ESTA_ACTIVO'    => new sfValidatorInteger(array('min' => -128, 'max' => 127, 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('receptor[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'Receptor';
  }


}
