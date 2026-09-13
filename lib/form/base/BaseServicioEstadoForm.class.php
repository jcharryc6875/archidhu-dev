<?php

/**
 * ServicioEstado form base class.
 *
 * @method ServicioEstado getObject() Returns the current form's model object
 *
 * @package    ##PROJECT_NAME##
 * @subpackage form
 * @author     ##AUTHOR_NAME##
 */
abstract class BaseServicioEstadoForm extends BaseFormPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'SERVICIOESTADO_ID' => new sfWidgetFormInputHidden(),
      'DESCRIPCION'       => new sfWidgetFormInputText(),
      'ADD_NOTE'          => new sfWidgetFormInputText(),
    ));

    $this->setValidators(array(
      'SERVICIOESTADO_ID' => new sfValidatorChoice(array('choices' => array($this->getObject()->getServicioestadoId()), 'empty_value' => $this->getObject()->getServicioestadoId(), 'required' => false)),
      'DESCRIPCION'       => new sfValidatorString(array('max_length' => 250, 'required' => false)),
      'ADD_NOTE'          => new sfValidatorInteger(array('min' => -2147483648, 'max' => 2147483647)),
    ));

    $this->widgetSchema->setNameFormat('servicio_estado[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'ServicioEstado';
  }


}
