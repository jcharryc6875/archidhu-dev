<?php

/**
 * Departamento form base class.
 *
 * @method Departamento getObject() Returns the current form's model object
 *
 * @package    ##PROJECT_NAME##
 * @subpackage form
 * @author     ##AUTHOR_NAME##
 */
abstract class BaseDepartamentoForm extends BaseFormPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'DEPARTAMENTO_ID' => new sfWidgetFormInputHidden(),
      'PAIS_ID'         => new sfWidgetFormPropelChoice(array('model' => 'Pais', 'add_empty' => false)),
      'NOMBRE'          => new sfWidgetFormInputText(),
    ));

    $this->setValidators(array(
      'DEPARTAMENTO_ID' => new sfValidatorChoice(array('choices' => array($this->getObject()->getDepartamentoId()), 'empty_value' => $this->getObject()->getDepartamentoId(), 'required' => false)),
      'PAIS_ID'         => new sfValidatorPropelChoice(array('model' => 'Pais', 'column' => 'PAIS_ID')),
      'NOMBRE'          => new sfValidatorString(array('max_length' => 100, 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('departamento[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'Departamento';
  }


}
