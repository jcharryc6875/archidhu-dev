<?php

/**
 * RolUsuariotransferencia form base class.
 *
 * @method RolUsuariotransferencia getObject() Returns the current form's model object
 *
 * @package    ##PROJECT_NAME##
 * @subpackage form
 * @author     ##AUTHOR_NAME##
 */
abstract class BaseRolUsuariotransferenciaForm extends BaseFormPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'ROLUSUARIOTRANSFERENCIA_ID' => new sfWidgetFormInputHidden(),
      'DESCRIPCION'                => new sfWidgetFormInputText(),
    ));

    $this->setValidators(array(
      'ROLUSUARIOTRANSFERENCIA_ID' => new sfValidatorChoice(array('choices' => array($this->getObject()->getRolusuariotransferenciaId()), 'empty_value' => $this->getObject()->getRolusuariotransferenciaId(), 'required' => false)),
      'DESCRIPCION'                => new sfValidatorString(array('max_length' => 250, 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('rol_usuariotransferencia[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'RolUsuariotransferencia';
  }


}
