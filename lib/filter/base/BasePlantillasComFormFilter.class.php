<?php

/**
 * PlantillasCom filter form base class.
 *
 * @package    simad
 * @subpackage filter
 * @author     Your name here
 */
abstract class BasePlantillasComFormFilter extends BaseFormFilterPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'MODULO_ID'        => new sfWidgetFormPropelChoice(array('model' => 'Modulo', 'add_empty' => true)),
      'REGIONAL_ID'      => new sfWidgetFormPropelChoice(array('model' => 'Regional', 'add_empty' => true)),
      'CODIGO'           => new sfWidgetFormFilterInput(),
      'DESCRIPCION'      => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'NOMBRE'           => new sfWidgetFormFilterInput(),
      'ES_ACTUAL'        => new sfWidgetFormFilterInput(),
      'CONTENTS'         => new sfWidgetFormFilterInput(),
    ));

    $this->setValidators(array(
      'MODULO_ID'        => new sfValidatorPropelChoice(array('required' => false, 'model' => 'Modulo', 'column' => 'MODULO_ID')),
      'REGIONAL_ID'      => new sfValidatorPropelChoice(array('required' => false, 'model' => 'Regional', 'column' => 'REGIONAL_ID')),
      'CODIGO'           => new sfValidatorPass(array('required' => false)),
      'DESCRIPCION'      => new sfValidatorPass(array('required' => false)),
      'NOMBRE'           => new sfValidatorPass(array('required' => false)),
      'ES_ACTUAL'        => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'CONTENTS'         => new sfValidatorPass(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('plantillas_com_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'PlantillasCom';
  }

  public function getFields()
  {
    return array(
      'PLANTILLASCOM_ID' => 'Number',
      'MODULO_ID'        => 'ForeignKey',
      'REGIONAL_ID'      => 'ForeignKey',
      'CODIGO'           => 'Text',
      'DESCRIPCION'      => 'Text',
      'NOMBRE'           => 'Text',
      'ES_ACTUAL'        => 'Number',
      'CONTENTS'         => 'Text',
    );
  }
}
