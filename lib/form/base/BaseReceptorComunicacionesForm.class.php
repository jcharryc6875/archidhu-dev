<?php

/**
 * ReceptorComunicaciones form base class.
 *
 * @method ReceptorComunicaciones getObject() Returns the current form's model object
 *
 * @package    ##PROJECT_NAME##
 * @subpackage form
 * @author     ##AUTHOR_NAME##
 */
abstract class BaseReceptorComunicacionesForm extends BaseFormPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'RECEPTORCOMUNICACIONES_ID' => new sfWidgetFormInputHidden(),
      'MODULO_ID'                 => new sfWidgetFormPropelChoice(array('model' => 'Modulo', 'add_empty' => false)),
      'USUARIO_ID'                => new sfWidgetFormPropelChoice(array('model' => 'Usuario', 'add_empty' => false)),
      'TIPOCOMRECIBIDA_ID'        => new sfWidgetFormPropelChoice(array('model' => 'TipoComRecibida', 'add_empty' => true)),
      'CANTIDAD'                  => new sfWidgetFormInputText(),
      'VARIACION'                 => new sfWidgetFormInputText(),
    ));

    $this->setValidators(array(
      'RECEPTORCOMUNICACIONES_ID' => new sfValidatorChoice(array('choices' => array($this->getObject()->getReceptorcomunicacionesId()), 'empty_value' => $this->getObject()->getReceptorcomunicacionesId(), 'required' => false)),
      'MODULO_ID'                 => new sfValidatorPropelChoice(array('model' => 'Modulo', 'column' => 'MODULO_ID')),
      'USUARIO_ID'                => new sfValidatorPropelChoice(array('model' => 'Usuario', 'column' => 'USUARIO_ID')),
      'TIPOCOMRECIBIDA_ID'        => new sfValidatorPropelChoice(array('model' => 'TipoComRecibida', 'column' => 'TIPOCOMRECIBIDA_ID', 'required' => false)),
      'CANTIDAD'                  => new sfValidatorInteger(array('min' => -2147483648, 'max' => 2147483647)),
      'VARIACION'                 => new sfValidatorInteger(array('min' => -2147483648, 'max' => 2147483647, 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('receptor_comunicaciones[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'ReceptorComunicaciones';
  }


}
