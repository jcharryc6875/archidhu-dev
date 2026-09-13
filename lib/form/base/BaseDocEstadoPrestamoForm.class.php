<?php

/**
 * DocEstadoPrestamo form base class.
 *
 * @method DocEstadoPrestamo getObject() Returns the current form's model object
 *
 * @package    ##PROJECT_NAME##
 * @subpackage form
 * @author     ##AUTHOR_NAME##
 */
abstract class BaseDocEstadoPrestamoForm extends BaseFormPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'DOCESTADOPRESTAMO_ID' => new sfWidgetFormInputHidden(),
      'DESCRIPCION'          => new sfWidgetFormInputText(),
    ));

    $this->setValidators(array(
      'DOCESTADOPRESTAMO_ID' => new sfValidatorChoice(array('choices' => array($this->getObject()->getDocestadoprestamoId()), 'empty_value' => $this->getObject()->getDocestadoprestamoId(), 'required' => false)),
      'DESCRIPCION'          => new sfValidatorString(array('max_length' => 250, 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('doc_estado_prestamo[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'DocEstadoPrestamo';
  }


}
