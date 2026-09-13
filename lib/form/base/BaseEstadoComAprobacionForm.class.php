<?php

/**
 * EstadoComAprobacion form base class.
 *
 * @method EstadoComAprobacion getObject() Returns the current form's model object
 *
 * @package    ##PROJECT_NAME##
 * @subpackage form
 * @author     ##AUTHOR_NAME##
 */
abstract class BaseEstadoComAprobacionForm extends BaseFormPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'ESTADOCOMAPROBACION_ID' => new sfWidgetFormInputHidden(),
      'DESCRIPCION'            => new sfWidgetFormInputText(),
    ));

    $this->setValidators(array(
      'ESTADOCOMAPROBACION_ID' => new sfValidatorChoice(array('choices' => array($this->getObject()->getEstadocomaprobacionId()), 'empty_value' => $this->getObject()->getEstadocomaprobacionId(), 'required' => false)),
      'DESCRIPCION'            => new sfValidatorString(array('max_length' => 50, 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('estado_com_aprobacion[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'EstadoComAprobacion';
  }


}
