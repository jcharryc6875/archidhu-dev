<?php

/**
 * FacturaEstado form base class.
 *
 * @method FacturaEstado getObject() Returns the current form's model object
 *
 * @package    ##PROJECT_NAME##
 * @subpackage form
 * @author     ##AUTHOR_NAME##
 */
abstract class BaseFacturaEstadoForm extends BaseFormPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'FACTURAESTADO_ID' => new sfWidgetFormInputHidden(),
      'DESCRIPCION'      => new sfWidgetFormInputText(),
    ));

    $this->setValidators(array(
      'FACTURAESTADO_ID' => new sfValidatorChoice(array('choices' => array($this->getObject()->getFacturaestadoId()), 'empty_value' => $this->getObject()->getFacturaestadoId(), 'required' => false)),
      'DESCRIPCION'      => new sfValidatorString(array('max_length' => 250, 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('factura_estado[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'FacturaEstado';
  }


}
