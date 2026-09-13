<?php

/**
 * ClEstadoPrestamo form base class.
 *
 * @method ClEstadoPrestamo getObject() Returns the current form's model object
 *
 * @package    ##PROJECT_NAME##
 * @subpackage form
 * @author     ##AUTHOR_NAME##
 */
abstract class BaseClEstadoPrestamoForm extends BaseFormPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'CLESTADOPRESTAMO_ID' => new sfWidgetFormInputHidden(),
      'DESCRIPCION'         => new sfWidgetFormInputText(),
    ));

    $this->setValidators(array(
      'CLESTADOPRESTAMO_ID' => new sfValidatorChoice(array('choices' => array($this->getObject()->getClestadoprestamoId()), 'empty_value' => $this->getObject()->getClestadoprestamoId(), 'required' => false)),
      'DESCRIPCION'         => new sfValidatorString(array('max_length' => 250, 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('cl_estado_prestamo[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'ClEstadoPrestamo';
  }


}
