<?php

/**
 * RolPrivilegio form base class.
 *
 * @method RolPrivilegio getObject() Returns the current form's model object
 *
 * @package    ##PROJECT_NAME##
 * @subpackage form
 * @author     ##AUTHOR_NAME##
 */
abstract class BaseRolPrivilegioForm extends BaseFormPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'ROLPRIVILEGIO_ID' => new sfWidgetFormInputHidden(),
      'ROL_ID'           => new sfWidgetFormPropelChoice(array('model' => 'Rol', 'add_empty' => false)),
      'FORMA_ID'         => new sfWidgetFormPropelChoice(array('model' => 'Forma', 'add_empty' => false)),
    ));

    $this->setValidators(array(
      'ROLPRIVILEGIO_ID' => new sfValidatorChoice(array('choices' => array($this->getObject()->getRolprivilegioId()), 'empty_value' => $this->getObject()->getRolprivilegioId(), 'required' => false)),
      'ROL_ID'           => new sfValidatorPropelChoice(array('model' => 'Rol', 'column' => 'ROL_ID')),
      'FORMA_ID'         => new sfValidatorPropelChoice(array('model' => 'Forma', 'column' => 'FORMA_ID')),
    ));

    $this->widgetSchema->setNameFormat('rol_privilegio[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'RolPrivilegio';
  }


}
