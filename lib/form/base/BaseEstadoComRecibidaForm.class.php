<?php

/**
 * EstadoComRecibida form base class.
 *
 * @method EstadoComRecibida getObject() Returns the current form's model object
 *
 * @package    ##PROJECT_NAME##
 * @subpackage form
 * @author     ##AUTHOR_NAME##
 */
abstract class BaseEstadoComRecibidaForm extends BaseFormPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'ESTADOCOMRECIBIDA_ID' => new sfWidgetFormInputHidden(),
      'DESCRIPCION'          => new sfWidgetFormInputText(),
      'ICONO'                => new sfWidgetFormInputText(),
    ));

    $this->setValidators(array(
      'ESTADOCOMRECIBIDA_ID' => new sfValidatorChoice(array('choices' => array($this->getObject()->getEstadocomrecibidaId()), 'empty_value' => $this->getObject()->getEstadocomrecibidaId(), 'required' => false)),
      'DESCRIPCION'          => new sfValidatorString(array('max_length' => 250, 'required' => false)),
      'ICONO'                => new sfValidatorString(array('max_length' => 100, 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('estado_com_recibida[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'EstadoComRecibida';
  }


}
