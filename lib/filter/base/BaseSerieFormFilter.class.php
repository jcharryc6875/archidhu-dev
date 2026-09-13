<?php

/**
 * Serie filter form base class.
 *
 * @package    simad
 * @subpackage filter
 * @author     Your name here
 */
abstract class BaseSerieFormFilter extends BaseFormFilterPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'DEPENDENCIA_ID'   => new sfWidgetFormPropelChoice(array('model' => 'Dependencia', 'add_empty' => true)),
      'CODIGO'           => new sfWidgetFormFilterInput(),
      'DESCRIPCION'      => new sfWidgetFormFilterInput(),
      'ES_IMPORTACIONES' => new sfWidgetFormFilterInput(),
    ));

    $this->setValidators(array(
      'DEPENDENCIA_ID'   => new sfValidatorPropelChoice(array('required' => false, 'model' => 'Dependencia', 'column' => 'DEPENDENCIA_ID')),
      'CODIGO'           => new sfValidatorPass(array('required' => false)),
      'DESCRIPCION'      => new sfValidatorPass(array('required' => false)),
      'ES_IMPORTACIONES' => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
    ));

    $this->widgetSchema->setNameFormat('serie_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'Serie';
  }

  public function getFields()
  {
    return array(
      'SERIE_ID'         => 'Number',
      'DEPENDENCIA_ID'   => 'ForeignKey',
      'CODIGO'           => 'Text',
      'DESCRIPCION'      => 'Text',
      'ES_IMPORTACIONES' => 'Number',
    );
  }
}
