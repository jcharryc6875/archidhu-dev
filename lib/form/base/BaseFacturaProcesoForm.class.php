<?php

/**
 * FacturaProceso form base class.
 *
 * @method FacturaProceso getObject() Returns the current form's model object
 *
 * @package    ##PROJECT_NAME##
 * @subpackage form
 * @author     ##AUTHOR_NAME##
 */
abstract class BaseFacturaProcesoForm extends BaseFormPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'FACTURAPROCESO_ID' => new sfWidgetFormInputHidden(),
      'DESCRIPCION'       => new sfWidgetFormInputText(),
    ));

    $this->setValidators(array(
      'FACTURAPROCESO_ID' => new sfValidatorChoice(array('choices' => array($this->getObject()->getFacturaprocesoId()), 'empty_value' => $this->getObject()->getFacturaprocesoId(), 'required' => false)),
      'DESCRIPCION'       => new sfValidatorString(array('max_length' => 250, 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('factura_proceso[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'FacturaProceso';
  }


}
