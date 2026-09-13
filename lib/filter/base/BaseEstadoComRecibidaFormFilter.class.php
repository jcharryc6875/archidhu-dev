<?php

/**
 * EstadoComRecibida filter form base class.
 *
 * @package    simad
 * @subpackage filter
 * @author     Your name here
 */
abstract class BaseEstadoComRecibidaFormFilter extends BaseFormFilterPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'DESCRIPCION'          => new sfWidgetFormFilterInput(),
      'ICONO'                => new sfWidgetFormFilterInput(),
    ));

    $this->setValidators(array(
      'DESCRIPCION'          => new sfValidatorPass(array('required' => false)),
      'ICONO'                => new sfValidatorPass(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('estado_com_recibida_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'EstadoComRecibida';
  }

  public function getFields()
  {
    return array(
      'ESTADOCOMRECIBIDA_ID' => 'Number',
      'DESCRIPCION'          => 'Text',
      'ICONO'                => 'Text',
    );
  }
}
