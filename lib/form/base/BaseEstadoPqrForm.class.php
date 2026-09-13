<?php

/**
 * EstadoPqr form base class.
 *
 * @method EstadoPqr getObject() Returns the current form's model object
 *
 * @package    ##PROJECT_NAME##
 * @subpackage form
 * @author     ##AUTHOR_NAME##
 */
abstract class BaseEstadoPqrForm extends BaseFormPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'ESTADOPQR_ID' => new sfWidgetFormInputHidden(),
      'DESCRIPCION'  => new sfWidgetFormInputText(),
    ));

    $this->setValidators(array(
      'ESTADOPQR_ID' => new sfValidatorChoice(array('choices' => array($this->getObject()->getEstadopqrId()), 'empty_value' => $this->getObject()->getEstadopqrId(), 'required' => false)),
      'DESCRIPCION'  => new sfValidatorString(array('max_length' => 250, 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('estado_pqr[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'EstadoPqr';
  }


}
