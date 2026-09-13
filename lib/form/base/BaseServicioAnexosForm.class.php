<?php

/**
 * ServicioAnexos form base class.
 *
 * @method ServicioAnexos getObject() Returns the current form's model object
 *
 * @package    ##PROJECT_NAME##
 * @subpackage form
 * @author     ##AUTHOR_NAME##
 */
abstract class BaseServicioAnexosForm extends BaseFormPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'SERVICIOANEXOS_ID' => new sfWidgetFormInputHidden(),
      'SERVICIO_ID'       => new sfWidgetFormPropelChoice(array('model' => 'Servicio', 'add_empty' => false)),
      'USUARIO_ID'        => new sfWidgetFormPropelChoice(array('model' => 'Usuario', 'add_empty' => false)),
      'DESCRIPCION'       => new sfWidgetFormInputText(),
      'RUTA'              => new sfWidgetFormInputText(),
      'FOLIOS'            => new sfWidgetFormInputText(),
      'FECHA_CREACION'    => new sfWidgetFormDateTime(),
      'ES_ACTUAL'         => new sfWidgetFormInputText(),
    ));

    $this->setValidators(array(
      'SERVICIOANEXOS_ID' => new sfValidatorChoice(array('choices' => array($this->getObject()->getServicioanexosId()), 'empty_value' => $this->getObject()->getServicioanexosId(), 'required' => false)),
      'SERVICIO_ID'       => new sfValidatorPropelChoice(array('model' => 'Servicio', 'column' => 'SERVICIO_ID')),
      'USUARIO_ID'        => new sfValidatorPropelChoice(array('model' => 'Usuario', 'column' => 'USUARIO_ID')),
      'DESCRIPCION'       => new sfValidatorString(array('max_length' => 800, 'required' => false)),
      'RUTA'              => new sfValidatorString(array('max_length' => 500, 'required' => false)),
      'FOLIOS'            => new sfValidatorInteger(array('min' => -2147483648, 'max' => 2147483647, 'required' => false)),
      'FECHA_CREACION'    => new sfValidatorDateTime(array('required' => false)),
      'ES_ACTUAL'         => new sfValidatorInteger(array('min' => -2147483648, 'max' => 2147483647, 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('servicio_anexos[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'ServicioAnexos';
  }


}
