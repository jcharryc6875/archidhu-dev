<?php

/**
 * EstadoContratista filter form base class.
 *
 * @package    simad
 * @subpackage filter
 * @author     Your name here
 */
abstract class BaseEstadoContratistaFormFilter extends BaseFormFilterPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'DESCRIPCION'          => new sfWidgetFormFilterInput(),
    ));

    $this->setValidators(array(
      'DESCRIPCION'          => new sfValidatorPass(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('estado_contratista_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'EstadoContratista';
  }

  public function getFields()
  {
    return array(
      'ESTADOCONTRATISTA_ID' => 'Number',
      'DESCRIPCION'          => 'Text',
    );
  }
}
