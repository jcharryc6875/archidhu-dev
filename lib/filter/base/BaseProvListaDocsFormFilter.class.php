<?php

/**
 * ProvListaDocs filter form base class.
 *
 * @package    simad
 * @subpackage filter
 * @author     Your name here
 */
abstract class BaseProvListaDocsFormFilter extends BaseFormFilterPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'NOMBRE'             => new sfWidgetFormFilterInput(),
      'DESCRIPCION'        => new sfWidgetFormFilterInput(),
    ));

    $this->setValidators(array(
      'NOMBRE'             => new sfValidatorPass(array('required' => false)),
      'DESCRIPCION'        => new sfValidatorPass(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('prov_lista_docs_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'ProvListaDocs';
  }

  public function getFields()
  {
    return array(
      'PROV_LISTA_DOCS_ID' => 'Number',
      'NOMBRE'             => 'Text',
      'DESCRIPCION'        => 'Text',
    );
  }
}
