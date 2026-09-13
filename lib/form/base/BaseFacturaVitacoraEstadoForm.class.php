<?php

/**
 * FacturaVitacoraEstado form base class.
 *
 * @method FacturaVitacoraEstado getObject() Returns the current form's model object
 *
 * @package    ##PROJECT_NAME##
 * @subpackage form
 * @author     ##AUTHOR_NAME##
 */
abstract class BaseFacturaVitacoraEstadoForm extends BaseFormPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'FACTURAVITACORAESTADO_ID' => new sfWidgetFormInputHidden(),
      'DESCRIPCION'              => new sfWidgetFormInputText(),
    ));

    $this->setValidators(array(
      'FACTURAVITACORAESTADO_ID' => new sfValidatorChoice(array('choices' => array($this->getObject()->getFacturavitacoraestadoId()), 'empty_value' => $this->getObject()->getFacturavitacoraestadoId(), 'required' => false)),
      'DESCRIPCION'              => new sfValidatorString(array('max_length' => 100, 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('factura_vitacora_estado[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'FacturaVitacoraEstado';
  }


}
