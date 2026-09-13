<?php

/**
 * RolCiudadInerna form base class.
 *
 * @method RolCiudadInerna getObject() Returns the current form's model object
 *
 * @package    ##PROJECT_NAME##
 * @subpackage form
 * @author     ##AUTHOR_NAME##
 */
abstract class BaseRolCiudadInernaForm extends BaseFormPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'ROLCIUDADINTERNA__ID' => new sfWidgetFormInputHidden(),
      'DESCRIPCION'          => new sfWidgetFormInputText(),
    ));

    $this->setValidators(array(
      'ROLCIUDADINTERNA__ID' => new sfValidatorChoice(array('choices' => array($this->getObject()->getRolciudadinternaId()), 'empty_value' => $this->getObject()->getRolciudadinternaId(), 'required' => false)),
      'DESCRIPCION'          => new sfValidatorString(array('max_length' => 250, 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('rol_ciudad_inerna[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'RolCiudadInerna';
  }


}
