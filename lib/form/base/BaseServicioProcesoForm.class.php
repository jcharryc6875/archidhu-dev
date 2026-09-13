<?php

/**
 * ServicioProceso form base class.
 *
 * @method ServicioProceso getObject() Returns the current form's model object
 *
 * @package    ##PROJECT_NAME##
 * @subpackage form
 * @author     ##AUTHOR_NAME##
 */
abstract class BaseServicioProcesoForm extends BaseFormPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'SERVICIOPROCESO_ID' => new sfWidgetFormInputHidden(),
      'DESCRIPCION'        => new sfWidgetFormInputText(),
      'ES_VISIBLE'         => new sfWidgetFormInputText(),
    ));

    $this->setValidators(array(
      'SERVICIOPROCESO_ID' => new sfValidatorChoice(array('choices' => array($this->getObject()->getServicioprocesoId()), 'empty_value' => $this->getObject()->getServicioprocesoId(), 'required' => false)),
      'DESCRIPCION'        => new sfValidatorString(array('max_length' => 250, 'required' => false)),
      'ES_VISIBLE'         => new sfValidatorInteger(array('min' => -2147483648, 'max' => 2147483647, 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('servicio_proceso[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'ServicioProceso';
  }


}
