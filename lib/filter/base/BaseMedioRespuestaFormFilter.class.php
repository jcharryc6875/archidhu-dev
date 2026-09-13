<?php

/**
 * MedioRespuesta filter form base class.
 *
 * @package    simad
 * @subpackage filter
 * @author     Your name here
 */
abstract class BaseMedioRespuestaFormFilter extends BaseFormFilterPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'DESCRIPCION'       => new sfWidgetFormFilterInput(),
      'ES_VISIBLE'        => new sfWidgetFormFilterInput(),
    ));

    $this->setValidators(array(
      'DESCRIPCION'       => new sfValidatorPass(array('required' => false)),
      'ES_VISIBLE'        => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
    ));

    $this->widgetSchema->setNameFormat('medio_respuesta_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'MedioRespuesta';
  }

  public function getFields()
  {
    return array(
      'MEDIORESPUESTA_ID' => 'Number',
      'DESCRIPCION'       => 'Text',
      'ES_VISIBLE'        => 'Number',
    );
  }
}
