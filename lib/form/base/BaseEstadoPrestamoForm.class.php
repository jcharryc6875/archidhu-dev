<?php

/**
 * EstadoPrestamo form base class.
 *
 * @method EstadoPrestamo getObject() Returns the current form's model object
 *
 * @package    ##PROJECT_NAME##
 * @subpackage form
 * @author     ##AUTHOR_NAME##
 */
abstract class BaseEstadoPrestamoForm extends BaseFormPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'ESTADOPRESTAMO_ID' => new sfWidgetFormInputHidden(),
      'DESCRIPCION'       => new sfWidgetFormInputText(),
    ));

    $this->setValidators(array(
      'ESTADOPRESTAMO_ID' => new sfValidatorChoice(array('choices' => array($this->getObject()->getEstadoprestamoId()), 'empty_value' => $this->getObject()->getEstadoprestamoId(), 'required' => false)),
      'DESCRIPCION'       => new sfValidatorString(array('max_length' => 250, 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('estado_prestamo[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'EstadoPrestamo';
  }


}
