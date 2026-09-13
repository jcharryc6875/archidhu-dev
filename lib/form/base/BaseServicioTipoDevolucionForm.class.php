<?php

/**
 * ServicioTipoDevolucion form base class.
 *
 * @method ServicioTipoDevolucion getObject() Returns the current form's model object
 *
 * @package    ##PROJECT_NAME##
 * @subpackage form
 * @author     ##AUTHOR_NAME##
 */
abstract class BaseServicioTipoDevolucionForm extends BaseFormPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'SERVICIOTIPODEVOLUCION_ID' => new sfWidgetFormInputHidden(),
      'DESCRIPCION'               => new sfWidgetFormInputText(),
      'ES_VISIBLE'                => new sfWidgetFormInputText(),
    ));

    $this->setValidators(array(
      'SERVICIOTIPODEVOLUCION_ID' => new sfValidatorChoice(array('choices' => array($this->getObject()->getServiciotipodevolucionId()), 'empty_value' => $this->getObject()->getServiciotipodevolucionId(), 'required' => false)),
      'DESCRIPCION'               => new sfValidatorString(array('max_length' => 100, 'required' => false)),
      'ES_VISIBLE'                => new sfValidatorInteger(array('min' => -2147483648, 'max' => 2147483647, 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('servicio_tipo_devolucion[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'ServicioTipoDevolucion';
  }


}
