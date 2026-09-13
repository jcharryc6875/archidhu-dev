<?php

/**
 * RolClusuario form base class.
 *
 * @method RolClusuario getObject() Returns the current form's model object
 *
 * @package    ##PROJECT_NAME##
 * @subpackage form
 * @author     ##AUTHOR_NAME##
 */
abstract class BaseRolClusuarioForm extends BaseFormPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'ROL_CLUSUARIO_ID' => new sfWidgetFormInputHidden(),
      'DESCRIPCION'      => new sfWidgetFormInputText(),
    ));

    $this->setValidators(array(
      'ROL_CLUSUARIO_ID' => new sfValidatorChoice(array('choices' => array($this->getObject()->getRolClusuarioId()), 'empty_value' => $this->getObject()->getRolClusuarioId(), 'required' => false)),
      'DESCRIPCION'      => new sfValidatorString(array('max_length' => 250, 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('rol_clusuario[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'RolClusuario';
  }


}
