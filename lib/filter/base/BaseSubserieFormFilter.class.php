<?php

/**
 * Subserie filter form base class.
 *
 * @package    simad
 * @subpackage filter
 * @author     Your name here
 */
abstract class BaseSubserieFormFilter extends BaseFormFilterPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'DESCARTEFINALSUBSERIE_ID'   => new sfWidgetFormPropelChoice(array('model' => 'DescarteFinalSubserie', 'add_empty' => true)),
      'SERIE_ID'                   => new sfWidgetFormPropelChoice(array('model' => 'Serie', 'add_empty' => true)),
      'CODIGO'                     => new sfWidgetFormFilterInput(),
      'DESCRIPCION'                => new sfWidgetFormFilterInput(),
      'ANOS_EN_GESTION'            => new sfWidgetFormFilterInput(),
      'ANOS_EN_CENTRAL'            => new sfWidgetFormFilterInput(),
      'ANOS_EN_HISTORICO'          => new sfWidgetFormFilterInput(),
      'ANOS_VALORACION_DOCUMENTAL' => new sfWidgetFormFilterInput(),
      'CODIGO2'                    => new sfWidgetFormFilterInput(),
      'PERIODO_SUBSERIE'           => new sfWidgetFormFilterInput(),
      'ES_MARCA'                   => new sfWidgetFormFilterInput(),
      'TIPOFIRMADIGITAL_ID'        => new sfWidgetFormFilterInput(),
      'PROCEDIMIENTO'              => new sfWidgetFormFilterInput(),
    ));

    $this->setValidators(array(
      'DESCARTEFINALSUBSERIE_ID'   => new sfValidatorPropelChoice(array('required' => false, 'model' => 'DescarteFinalSubserie', 'column' => 'DESCARTEFINALSUBSERIE_ID')),
      'SERIE_ID'                   => new sfValidatorPropelChoice(array('required' => false, 'model' => 'Serie', 'column' => 'SERIE_ID')),
      'CODIGO'                     => new sfValidatorPass(array('required' => false)),
      'DESCRIPCION'                => new sfValidatorPass(array('required' => false)),
      'ANOS_EN_GESTION'            => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'ANOS_EN_CENTRAL'            => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'ANOS_EN_HISTORICO'          => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'ANOS_VALORACION_DOCUMENTAL' => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'CODIGO2'                    => new sfValidatorPass(array('required' => false)),
      'PERIODO_SUBSERIE'           => new sfValidatorPass(array('required' => false)),
      'ES_MARCA'                   => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'TIPOFIRMADIGITAL_ID'        => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'PROCEDIMIENTO'              => new sfValidatorPass(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('subserie_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'Subserie';
  }

  public function getFields()
  {
    return array(
      'SUBSERIE_ID'                => 'Number',
      'DESCARTEFINALSUBSERIE_ID'   => 'ForeignKey',
      'SERIE_ID'                   => 'ForeignKey',
      'CODIGO'                     => 'Text',
      'DESCRIPCION'                => 'Text',
      'ANOS_EN_GESTION'            => 'Number',
      'ANOS_EN_CENTRAL'            => 'Number',
      'ANOS_EN_HISTORICO'          => 'Number',
      'ANOS_VALORACION_DOCUMENTAL' => 'Number',
      'CODIGO2'                    => 'Text',
      'PERIODO_SUBSERIE'           => 'Text',
      'ES_MARCA'                   => 'Number',
      'TIPOFIRMADIGITAL_ID'        => 'Number',
      'PROCEDIMIENTO'              => 'Text',
    );
  }
}
