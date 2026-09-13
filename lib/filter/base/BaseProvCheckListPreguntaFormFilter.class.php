<?php

/**
 * ProvCheckListPregunta filter form base class.
 *
 * @package    simad
 * @subpackage filter
 * @author     Your name here
 */
abstract class BaseProvCheckListPreguntaFormFilter extends BaseFormFilterPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'PROV_LISTA_DOCS_ID'          => new sfWidgetFormPropelChoice(array('model' => 'ProvListaDocs', 'add_empty' => true)),
      'DESCRIPCION'                 => new sfWidgetFormFilterInput(),
    ));

    $this->setValidators(array(
      'PROV_LISTA_DOCS_ID'          => new sfValidatorPropelChoice(array('required' => false, 'model' => 'ProvListaDocs', 'column' => 'PROV_LISTA_DOCS_ID')),
      'DESCRIPCION'                 => new sfValidatorPass(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('prov_check_list_pregunta_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'ProvCheckListPregunta';
  }

  public function getFields()
  {
    return array(
      'PROV_CHECK_LIST_PREGUNTA_ID' => 'Number',
      'PROV_LISTA_DOCS_ID'          => 'ForeignKey',
      'DESCRIPCION'                 => 'Text',
    );
  }
}
