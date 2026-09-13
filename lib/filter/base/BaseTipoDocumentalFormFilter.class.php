<?php

/**
 * TipoDocumental filter form base class.
 *
 * @package    simad
 * @subpackage filter
 * @author     Your name here
 */
abstract class BaseTipoDocumentalFormFilter extends BaseFormFilterPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'SUBSERIE_ID'       => new sfWidgetFormPropelChoice(array('model' => 'Subserie', 'add_empty' => true)),
      'CODIGO'            => new sfWidgetFormFilterInput(),
      'DESCRIPCION'       => new sfWidgetFormFilterInput(),
      'ORDEN'             => new sfWidgetFormFilterInput(),
      'ES_FORMATO'        => new sfWidgetFormFilterInput(),
    ));

    $this->setValidators(array(
      'SUBSERIE_ID'       => new sfValidatorPropelChoice(array('required' => false, 'model' => 'Subserie', 'column' => 'SUBSERIE_ID')),
      'CODIGO'            => new sfValidatorPass(array('required' => false)),
      'DESCRIPCION'       => new sfValidatorPass(array('required' => false)),
      'ORDEN'             => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'ES_FORMATO'        => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
    ));

    $this->widgetSchema->setNameFormat('tipo_documental_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'TipoDocumental';
  }

  public function getFields()
  {
    return array(
      'TIPODOCUMENTAL_ID' => 'Number',
      'SUBSERIE_ID'       => 'ForeignKey',
      'CODIGO'            => 'Text',
      'DESCRIPCION'       => 'Text',
      'ORDEN'             => 'Number',
      'ES_FORMATO'        => 'Number',
    );
  }
}
