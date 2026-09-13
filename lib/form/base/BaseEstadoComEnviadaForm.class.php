<?php

/**
 * EstadoComEnviada form base class.
 *
 * @method EstadoComEnviada getObject() Returns the current form's model object
 *
 * @package    ##PROJECT_NAME##
 * @subpackage form
 * @author     ##AUTHOR_NAME##
 */
abstract class BaseEstadoComEnviadaForm extends BaseFormPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'ESTADOCOMENVIADA_ID' => new sfWidgetFormInputHidden(),
      'DESCRIPCION'         => new sfWidgetFormInputText(),
    ));

    $this->setValidators(array(
      'ESTADOCOMENVIADA_ID' => new sfValidatorChoice(array('choices' => array($this->getObject()->getEstadocomenviadaId()), 'empty_value' => $this->getObject()->getEstadocomenviadaId(), 'required' => false)),
      'DESCRIPCION'         => new sfValidatorString(array('max_length' => 250, 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('estado_com_enviada[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'EstadoComEnviada';
  }


}
