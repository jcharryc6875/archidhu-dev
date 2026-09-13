<?php

/**
 * EspecialidadPlano form base class.
 *
 * @method EspecialidadPlano getObject() Returns the current form's model object
 *
 * @package    ##PROJECT_NAME##
 * @subpackage form
 * @author     ##AUTHOR_NAME##
 */
abstract class BaseEspecialidadPlanoForm extends BaseFormPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'ESPECIALIDADPLANO_ID' => new sfWidgetFormInputHidden(),
      'DESCRIPCION'          => new sfWidgetFormInputText(),
    ));

    $this->setValidators(array(
      'ESPECIALIDADPLANO_ID' => new sfValidatorChoice(array('choices' => array($this->getObject()->getEspecialidadplanoId()), 'empty_value' => $this->getObject()->getEspecialidadplanoId(), 'required' => false)),
      'DESCRIPCION'          => new sfValidatorString(array('max_length' => 250, 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('especialidad_plano[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'EspecialidadPlano';
  }


}
