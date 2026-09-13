<?php

/**
 * PrioridadPqr form base class.
 *
 * @method PrioridadPqr getObject() Returns the current form's model object
 *
 * @package    ##PROJECT_NAME##
 * @subpackage form
 * @author     ##AUTHOR_NAME##
 */
abstract class BasePrioridadPqrForm extends BaseFormPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'PRIORIDADPQR_ID' => new sfWidgetFormInputHidden(),
      'DESCRIPCION'     => new sfWidgetFormInputText(),
    ));

    $this->setValidators(array(
      'PRIORIDADPQR_ID' => new sfValidatorChoice(array('choices' => array($this->getObject()->getPrioridadpqrId()), 'empty_value' => $this->getObject()->getPrioridadpqrId(), 'required' => false)),
      'DESCRIPCION'     => new sfValidatorString(array('max_length' => 250, 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('prioridad_pqr[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'PrioridadPqr';
  }


}
