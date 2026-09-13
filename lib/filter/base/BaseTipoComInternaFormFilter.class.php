<?php

/**
 * TipoComInterna filter form base class.
 *
 * @package    simad
 * @subpackage filter
 * @author     Your name here
 */
abstract class BaseTipoComInternaFormFilter extends BaseFormFilterPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'PLANTILLASCOM_ID'  => new sfWidgetFormPropelChoice(array('model' => 'PlantillasCom', 'add_empty' => true)),
      'DESCRIPCION'       => new sfWidgetFormFilterInput(),
      'ES_VISIBLE'        => new sfWidgetFormFilterInput(),
    ));

    $this->setValidators(array(
      'PLANTILLASCOM_ID'  => new sfValidatorPropelChoice(array('required' => false, 'model' => 'PlantillasCom', 'column' => 'PLANTILLASCOM_ID')),
      'DESCRIPCION'       => new sfValidatorPass(array('required' => false)),
      'ES_VISIBLE'        => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
    ));

    $this->widgetSchema->setNameFormat('tipo_com_interna_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'TipoComInterna';
  }

  public function getFields()
  {
    return array(
      'TIPOCOMINTERNA_ID' => 'Number',
      'PLANTILLASCOM_ID'  => 'ForeignKey',
      'DESCRIPCION'       => 'Text',
      'ES_VISIBLE'        => 'Number',
    );
  }
}
