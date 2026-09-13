<?php

/**
 * TipoEscala form base class.
 *
 * @method TipoEscala getObject() Returns the current form's model object
 *
 * @package    ##PROJECT_NAME##
 * @subpackage form
 * @author     ##AUTHOR_NAME##
 */
abstract class BaseTipoEscalaForm extends BaseFormPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'TIPOESCALA_ID' => new sfWidgetFormInputHidden(),
      'DESCRIPCION'   => new sfWidgetFormInputText(),
    ));

    $this->setValidators(array(
      'TIPOESCALA_ID' => new sfValidatorChoice(array('choices' => array($this->getObject()->getTipoescalaId()), 'empty_value' => $this->getObject()->getTipoescalaId(), 'required' => false)),
      'DESCRIPCION'   => new sfValidatorString(array('max_length' => 250, 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('tipo_escala[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'TipoEscala';
  }


}
