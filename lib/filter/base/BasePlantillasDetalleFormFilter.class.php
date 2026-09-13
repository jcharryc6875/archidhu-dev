<?php

/**
 * PlantillasDetalle filter form base class.
 *
 * @package    simad
 * @subpackage filter
 * @author     Your name here
 */
abstract class BasePlantillasDetalleFormFilter extends BaseFormFilterPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'CODIGO'               => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'DESCRIPCION'          => new sfWidgetFormFilterInput(),
      'HTML_DATA'            => new sfWidgetFormFilterInput(),
      'ES_VISIBLE'           => new sfWidgetFormFilterInput(array('with_empty' => false)),
    ));

    $this->setValidators(array(
      'CODIGO'               => new sfValidatorPass(array('required' => false)),
      'DESCRIPCION'          => new sfValidatorPass(array('required' => false)),
      'HTML_DATA'            => new sfValidatorPass(array('required' => false)),
      'ES_VISIBLE'           => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
    ));

    $this->widgetSchema->setNameFormat('plantillas_detalle_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'PlantillasDetalle';
  }

  public function getFields()
  {
    return array(
      'PLANTILLASDETALLE_ID' => 'Number',
      'CODIGO'               => 'Text',
      'DESCRIPCION'          => 'Text',
      'HTML_DATA'            => 'Text',
      'ES_VISIBLE'           => 'Number',
    );
  }
}
