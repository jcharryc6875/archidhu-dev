<?php

/**
 * FactProcesoPorTipo form base class.
 *
 * @method FactProcesoPorTipo getObject() Returns the current form's model object
 *
 * @package    ##PROJECT_NAME##
 * @subpackage form
 * @author     ##AUTHOR_NAME##
 */
abstract class BaseFactProcesoPorTipoForm extends BaseFormPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'FACTPROCESOXTIPO_ID' => new sfWidgetFormInputHidden(),
      'FACTURAPROCESO_ID'   => new sfWidgetFormInputHidden(),
      'FACTURATIPO_ID'      => new sfWidgetFormInputHidden(),
      'FACTURAESTADO_ID'    => new sfWidgetFormInputHidden(),
    ));

    $this->setValidators(array(
      'FACTPROCESOXTIPO_ID' => new sfValidatorChoice(array('choices' => array($this->getObject()->getFactprocesoxtipoId()), 'empty_value' => $this->getObject()->getFactprocesoxtipoId(), 'required' => false)),
      'FACTURAPROCESO_ID'   => new sfValidatorPropelChoice(array('model' => 'FacturaProceso', 'column' => 'FACTURAPROCESO_ID', 'required' => false)),
      'FACTURATIPO_ID'      => new sfValidatorPropelChoice(array('model' => 'FacturaTipo', 'column' => 'FACTURATIPO_ID', 'required' => false)),
      'FACTURAESTADO_ID'    => new sfValidatorPropelChoice(array('model' => 'FacturaEstado', 'column' => 'FACTURAESTADO_ID', 'required' => false)),
    ));

    $this->validatorSchema->setPostValidator(
      new sfValidatorPropelUnique(array('model' => 'FactProcesoPorTipo', 'column' => array('FACTURATIPO_ID')))
    );

    $this->widgetSchema->setNameFormat('fact_proceso_por_tipo[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'FactProcesoPorTipo';
  }


}
