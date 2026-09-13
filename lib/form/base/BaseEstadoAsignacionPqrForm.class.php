<?php

/**
 * EstadoAsignacionPqr form base class.
 *
 * @method EstadoAsignacionPqr getObject() Returns the current form's model object
 *
 * @package    ##PROJECT_NAME##
 * @subpackage form
 * @author     ##AUTHOR_NAME##
 */
abstract class BaseEstadoAsignacionPqrForm extends BaseFormPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'ESTADOASIGNACIONPQR_ID' => new sfWidgetFormInputHidden(),
      'DESCRIPCION'            => new sfWidgetFormInputText(),
    ));

    $this->setValidators(array(
      'ESTADOASIGNACIONPQR_ID' => new sfValidatorChoice(array('choices' => array($this->getObject()->getEstadoasignacionpqrId()), 'empty_value' => $this->getObject()->getEstadoasignacionpqrId(), 'required' => false)),
      'DESCRIPCION'            => new sfValidatorString(array('max_length' => 250, 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('estado_asignacion_pqr[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'EstadoAsignacionPqr';
  }


}
