<?php

/**
 * ProvCheckListPregunta form base class.
 *
 * @method ProvCheckListPregunta getObject() Returns the current form's model object
 *
 * @package    ##PROJECT_NAME##
 * @subpackage form
 * @author     ##AUTHOR_NAME##
 */
abstract class BaseProvCheckListPreguntaForm extends BaseFormPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'PROV_CHECK_LIST_PREGUNTA_ID' => new sfWidgetFormInputHidden(),
      'PROV_LISTA_DOCS_ID'          => new sfWidgetFormPropelChoice(array('model' => 'ProvListaDocs', 'add_empty' => false)),
      'DESCRIPCION'                 => new sfWidgetFormInputText(),
    ));

    $this->setValidators(array(
      'PROV_CHECK_LIST_PREGUNTA_ID' => new sfValidatorChoice(array('choices' => array($this->getObject()->getProvCheckListPreguntaId()), 'empty_value' => $this->getObject()->getProvCheckListPreguntaId(), 'required' => false)),
      'PROV_LISTA_DOCS_ID'          => new sfValidatorPropelChoice(array('model' => 'ProvListaDocs', 'column' => 'PROV_LISTA_DOCS_ID')),
      'DESCRIPCION'                 => new sfValidatorString(array('max_length' => 250, 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('prov_check_list_pregunta[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'ProvCheckListPregunta';
  }


}
