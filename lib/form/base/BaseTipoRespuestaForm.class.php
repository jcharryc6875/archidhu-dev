<?php

/**
 * TipoRespuesta form base class.
 *
 * @method TipoRespuesta getObject() Returns the current form's model object
 *
 * @package    ##PROJECT_NAME##
 * @subpackage form
 * @author     ##AUTHOR_NAME##
 */
abstract class BaseTipoRespuestaForm extends BaseFormPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'TIPORESPUESTA_ID' => new sfWidgetFormInputHidden(),
      'DESCRIPCION'      => new sfWidgetFormInputText(),
      'ES_VISIBLE'       => new sfWidgetFormInputText(),
    ));

    $this->setValidators(array(
      'TIPORESPUESTA_ID' => new sfValidatorChoice(array('choices' => array($this->getObject()->getTiporespuestaId()), 'empty_value' => $this->getObject()->getTiporespuestaId(), 'required' => false)),
      'DESCRIPCION'      => new sfValidatorString(array('max_length' => 50, 'required' => false)),
      'ES_VISIBLE'       => new sfValidatorInteger(array('min' => -2147483648, 'max' => 2147483647, 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('tipo_respuesta[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'TipoRespuesta';
  }


}
