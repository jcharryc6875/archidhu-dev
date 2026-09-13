<?php

/**
 * FacturaReceptor form base class.
 *
 * @method FacturaReceptor getObject() Returns the current form's model object
 *
 * @package    ##PROJECT_NAME##
 * @subpackage form
 * @author     ##AUTHOR_NAME##
 */
abstract class BaseFacturaReceptorForm extends BaseFormPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'FACTURARECEPTOR_ID' => new sfWidgetFormInputHidden(),
      'FACTURAPROCESO_ID'  => new sfWidgetFormPropelChoice(array('model' => 'FacturaProceso', 'add_empty' => false)),
      'REGIONAL_ID'        => new sfWidgetFormPropelChoice(array('model' => 'Regional', 'add_empty' => false)),
      'USUARIO_ID'         => new sfWidgetFormPropelChoice(array('model' => 'Usuario', 'add_empty' => false)),
    ));

    $this->setValidators(array(
      'FACTURARECEPTOR_ID' => new sfValidatorChoice(array('choices' => array($this->getObject()->getFacturareceptorId()), 'empty_value' => $this->getObject()->getFacturareceptorId(), 'required' => false)),
      'FACTURAPROCESO_ID'  => new sfValidatorPropelChoice(array('model' => 'FacturaProceso', 'column' => 'FACTURAPROCESO_ID')),
      'REGIONAL_ID'        => new sfValidatorPropelChoice(array('model' => 'Regional', 'column' => 'REGIONAL_ID')),
      'USUARIO_ID'         => new sfValidatorPropelChoice(array('model' => 'Usuario', 'column' => 'USUARIO_ID')),
    ));

    $this->widgetSchema->setNameFormat('factura_receptor[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'FacturaReceptor';
  }


}
