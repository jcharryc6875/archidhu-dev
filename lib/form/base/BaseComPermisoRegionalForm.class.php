<?php

/**
 * ComPermisoRegional form base class.
 *
 * @method ComPermisoRegional getObject() Returns the current form's model object
 *
 * @package    ##PROJECT_NAME##
 * @subpackage form
 * @author     ##AUTHOR_NAME##
 */
abstract class BaseComPermisoRegionalForm extends BaseFormPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'COMPERMISOREGIONAL_ID' => new sfWidgetFormInputHidden(),
      'REGIONAL_ID'           => new sfWidgetFormPropelChoice(array('model' => 'Regional', 'add_empty' => false)),
      'USUARIO_ID'            => new sfWidgetFormPropelChoice(array('model' => 'Usuario', 'add_empty' => false)),
    ));

    $this->setValidators(array(
      'COMPERMISOREGIONAL_ID' => new sfValidatorChoice(array('choices' => array($this->getObject()->getCompermisoregionalId()), 'empty_value' => $this->getObject()->getCompermisoregionalId(), 'required' => false)),
      'REGIONAL_ID'           => new sfValidatorPropelChoice(array('model' => 'Regional', 'column' => 'REGIONAL_ID')),
      'USUARIO_ID'            => new sfValidatorPropelChoice(array('model' => 'Usuario', 'column' => 'USUARIO_ID')),
    ));

    $this->widgetSchema->setNameFormat('com_permiso_regional[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'ComPermisoRegional';
  }


}
