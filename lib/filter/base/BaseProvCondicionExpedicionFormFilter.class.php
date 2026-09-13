<?php

/**
 * ProvCondicionExpedicion filter form base class.
 *
 * @package    simad
 * @subpackage filter
 * @author     Your name here
 */
abstract class BaseProvCondicionExpedicionFormFilter extends BaseFormFilterPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'CODIGO'                       => new sfWidgetFormFilterInput(),
      'DESCRIPCION'                  => new sfWidgetFormFilterInput(),
    ));

    $this->setValidators(array(
      'CODIGO'                       => new sfValidatorPass(array('required' => false)),
      'DESCRIPCION'                  => new sfValidatorPass(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('prov_condicion_expedicion_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'ProvCondicionExpedicion';
  }

  public function getFields()
  {
    return array(
      'PROV_CONDICION_EXPEDICION_ID' => 'Number',
      'CODIGO'                       => 'Text',
      'DESCRIPCION'                  => 'Text',
    );
  }
}
