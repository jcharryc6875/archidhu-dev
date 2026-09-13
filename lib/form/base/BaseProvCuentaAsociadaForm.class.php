<?php

/**
 * ProvCuentaAsociada form base class.
 *
 * @method ProvCuentaAsociada getObject() Returns the current form's model object
 *
 * @package    ##PROJECT_NAME##
 * @subpackage form
 * @author     ##AUTHOR_NAME##
 */
abstract class BaseProvCuentaAsociadaForm extends BaseFormPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'PROV_CUENTA_ASOCIADA_ID' => new sfWidgetFormInputHidden(),
      'PAIS_ID'                 => new sfWidgetFormPropelChoice(array('model' => 'Pais', 'add_empty' => false)),
      'DESCRIPCION'             => new sfWidgetFormInputText(),
      'CODIGO'                  => new sfWidgetFormInputText(),
    ));

    $this->setValidators(array(
      'PROV_CUENTA_ASOCIADA_ID' => new sfValidatorChoice(array('choices' => array($this->getObject()->getProvCuentaAsociadaId()), 'empty_value' => $this->getObject()->getProvCuentaAsociadaId(), 'required' => false)),
      'PAIS_ID'                 => new sfValidatorPropelChoice(array('model' => 'Pais', 'column' => 'PAIS_ID')),
      'DESCRIPCION'             => new sfValidatorString(array('max_length' => 250, 'required' => false)),
      'CODIGO'                  => new sfValidatorString(array('max_length' => 50, 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('prov_cuenta_asociada[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'ProvCuentaAsociada';
  }


}
