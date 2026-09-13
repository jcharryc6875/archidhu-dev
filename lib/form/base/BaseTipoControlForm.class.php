<?php

/**
 * TipoControl form base class.
 *
 * @method TipoControl getObject() Returns the current form's model object
 *
 * @package    ##PROJECT_NAME##
 * @subpackage form
 * @author     ##AUTHOR_NAME##
 */
abstract class BaseTipoControlForm extends BaseFormPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'TIPOCONTROL_ID' => new sfWidgetFormInputHidden(),
      'MODULO_ID'      => new sfWidgetFormPropelChoice(array('model' => 'Modulo', 'add_empty' => false)),
      'DESCRIPCION'    => new sfWidgetFormInputText(),
    ));

    $this->setValidators(array(
      'TIPOCONTROL_ID' => new sfValidatorChoice(array('choices' => array($this->getObject()->getTipocontrolId()), 'empty_value' => $this->getObject()->getTipocontrolId(), 'required' => false)),
      'MODULO_ID'      => new sfValidatorPropelChoice(array('model' => 'Modulo', 'column' => 'MODULO_ID')),
      'DESCRIPCION'    => new sfValidatorString(array('max_length' => 200, 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('tipo_control[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'TipoControl';
  }


}
