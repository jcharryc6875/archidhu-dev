<?php

/**
 * FacturaDevolucion form base class.
 *
 * @method FacturaDevolucion getObject() Returns the current form's model object
 *
 * @package    ##PROJECT_NAME##
 * @subpackage form
 * @author     ##AUTHOR_NAME##
 */
abstract class BaseFacturaDevolucionForm extends BaseFormPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'FACTURADEVOLUCION_ID' => new sfWidgetFormInputHidden(),
      'FAPROCESOACTUAL_ID'   => new sfWidgetFormPropelChoice(array('model' => 'FacturaProceso', 'add_empty' => false)),
      'FAESTADOACTUAL_ID'    => new sfWidgetFormPropelChoice(array('model' => 'FacturaEstado', 'add_empty' => false)),
      'FAPROCESODESTINO_ID'  => new sfWidgetFormPropelChoice(array('model' => 'FacturaProceso', 'add_empty' => false)),
      'FAESTADODESTINO_ID'   => new sfWidgetFormPropelChoice(array('model' => 'FacturaEstado', 'add_empty' => false)),
      'FACTURATIPO_ID'       => new sfWidgetFormPropelChoice(array('model' => 'FacturaTipo', 'add_empty' => true)),
    ));

    $this->setValidators(array(
      'FACTURADEVOLUCION_ID' => new sfValidatorChoice(array('choices' => array($this->getObject()->getFacturadevolucionId()), 'empty_value' => $this->getObject()->getFacturadevolucionId(), 'required' => false)),
      'FAPROCESOACTUAL_ID'   => new sfValidatorPropelChoice(array('model' => 'FacturaProceso', 'column' => 'FACTURAPROCESO_ID')),
      'FAESTADOACTUAL_ID'    => new sfValidatorPropelChoice(array('model' => 'FacturaEstado', 'column' => 'FACTURAESTADO_ID')),
      'FAPROCESODESTINO_ID'  => new sfValidatorPropelChoice(array('model' => 'FacturaProceso', 'column' => 'FACTURAPROCESO_ID')),
      'FAESTADODESTINO_ID'   => new sfValidatorPropelChoice(array('model' => 'FacturaEstado', 'column' => 'FACTURAESTADO_ID')),
      'FACTURATIPO_ID'       => new sfValidatorPropelChoice(array('model' => 'FacturaTipo', 'column' => 'FACTURATIPO_ID', 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('factura_devolucion[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'FacturaDevolucion';
  }


}
