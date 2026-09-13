<?php

/**
 * SubseriePorUsuario form base class.
 *
 * @method SubseriePorUsuario getObject() Returns the current form's model object
 *
 * @package    ##PROJECT_NAME##
 * @subpackage form
 * @author     ##AUTHOR_NAME##
 */
abstract class BaseSubseriePorUsuarioForm extends BaseFormPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'SUBSERIEPORUSUARIO_ID' => new sfWidgetFormInputHidden(),
      'SUBSERIE_ID'           => new sfWidgetFormPropelChoice(array('model' => 'Subserie', 'add_empty' => false)),
      'USUARIO_ID'            => new sfWidgetFormPropelChoice(array('model' => 'Usuario', 'add_empty' => false)),
      'CREACION'              => new sfWidgetFormInputText(),
      'PRESTAMO'              => new sfWidgetFormInputText(),
      'VISUALIZACION'         => new sfWidgetFormInputText(),
    ));

    $this->setValidators(array(
      'SUBSERIEPORUSUARIO_ID' => new sfValidatorChoice(array('choices' => array($this->getObject()->getSubserieporusuarioId()), 'empty_value' => $this->getObject()->getSubserieporusuarioId(), 'required' => false)),
      'SUBSERIE_ID'           => new sfValidatorPropelChoice(array('model' => 'Subserie', 'column' => 'SUBSERIE_ID')),
      'USUARIO_ID'            => new sfValidatorPropelChoice(array('model' => 'Usuario', 'column' => 'USUARIO_ID')),
      'CREACION'              => new sfValidatorInteger(array('min' => -128, 'max' => 127, 'required' => false)),
      'PRESTAMO'              => new sfValidatorInteger(array('min' => -128, 'max' => 127, 'required' => false)),
      'VISUALIZACION'         => new sfValidatorInteger(array('min' => -128, 'max' => 127, 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('subserie_por_usuario[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'SubseriePorUsuario';
  }


}
