<?php

/**
 * TipoIdentificacion form base class.
 *
 * @method TipoIdentificacion getObject() Returns the current form's model object
 *
 * @package    ##PROJECT_NAME##
 * @subpackage form
 * @author     ##AUTHOR_NAME##
 */
abstract class BaseTipoIdentificacionForm extends BaseFormPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'TIPOIDENTIFICACION_ID' => new sfWidgetFormInputHidden(),
      'DESCRIPCION'           => new sfWidgetFormInputText(),
    ));

    $this->setValidators(array(
      'TIPOIDENTIFICACION_ID' => new sfValidatorChoice(array('choices' => array($this->getObject()->getTipoidentificacionId()), 'empty_value' => $this->getObject()->getTipoidentificacionId(), 'required' => false)),
      'DESCRIPCION'           => new sfValidatorString(array('max_length' => 50, 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('tipo_identificacion[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'TipoIdentificacion';
  }


}
