<?php

/**
 * FacturaReceptor filter form base class.
 *
 * @package    simad
 * @subpackage filter
 * @author     Your name here
 */
abstract class BaseFacturaReceptorFormFilter extends BaseFormFilterPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'FACTURAPROCESO_ID'  => new sfWidgetFormPropelChoice(array('model' => 'FacturaProceso', 'add_empty' => true)),
      'REGIONAL_ID'        => new sfWidgetFormPropelChoice(array('model' => 'Regional', 'add_empty' => true)),
      'USUARIO_ID'         => new sfWidgetFormPropelChoice(array('model' => 'Usuario', 'add_empty' => true)),
    ));

    $this->setValidators(array(
      'FACTURAPROCESO_ID'  => new sfValidatorPropelChoice(array('required' => false, 'model' => 'FacturaProceso', 'column' => 'FACTURAPROCESO_ID')),
      'REGIONAL_ID'        => new sfValidatorPropelChoice(array('required' => false, 'model' => 'Regional', 'column' => 'REGIONAL_ID')),
      'USUARIO_ID'         => new sfValidatorPropelChoice(array('required' => false, 'model' => 'Usuario', 'column' => 'USUARIO_ID')),
    ));

    $this->widgetSchema->setNameFormat('factura_receptor_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'FacturaReceptor';
  }

  public function getFields()
  {
    return array(
      'FACTURARECEPTOR_ID' => 'Number',
      'FACTURAPROCESO_ID'  => 'ForeignKey',
      'REGIONAL_ID'        => 'ForeignKey',
      'USUARIO_ID'         => 'ForeignKey',
    );
  }
}
