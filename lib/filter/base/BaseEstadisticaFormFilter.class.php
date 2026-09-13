<?php

/**
 * Estadistica filter form base class.
 *
 * @package    simad
 * @subpackage filter
 * @author     Your name here
 */
abstract class BaseEstadisticaFormFilter extends BaseFormFilterPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'ORDEN'          => new sfWidgetFormFilterInput(),
      'MODULO'         => new sfWidgetFormFilterInput(),
      'NOMBRE'         => new sfWidgetFormFilterInput(),
      'DESCRIPCION'    => new sfWidgetFormFilterInput(),
      'CODIGOSQL'      => new sfWidgetFormFilterInput(),
    ));

    $this->setValidators(array(
      'ORDEN'          => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'MODULO'         => new sfValidatorPass(array('required' => false)),
      'NOMBRE'         => new sfValidatorPass(array('required' => false)),
      'DESCRIPCION'    => new sfValidatorPass(array('required' => false)),
      'CODIGOSQL'      => new sfValidatorPass(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('estadistica_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'Estadistica';
  }

  public function getFields()
  {
    return array(
      'ESTADISTICA_ID' => 'Number',
      'ORDEN'          => 'Number',
      'MODULO'         => 'Text',
      'NOMBRE'         => 'Text',
      'DESCRIPCION'    => 'Text',
      'CODIGOSQL'      => 'Text',
    );
  }
}
