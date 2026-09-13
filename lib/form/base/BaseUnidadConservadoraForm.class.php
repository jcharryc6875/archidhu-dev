<?php

/**
 * UnidadConservadora form base class.
 *
 * @method UnidadConservadora getObject() Returns the current form's model object
 *
 * @package    ##PROJECT_NAME##
 * @subpackage form
 * @author     ##AUTHOR_NAME##
 */
abstract class BaseUnidadConservadoraForm extends BaseFormPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'UNIDADCONSERVADORA_ID' => new sfWidgetFormInputHidden(),
      'DESCRIPCION'           => new sfWidgetFormInputText(),
    ));

    $this->setValidators(array(
      'UNIDADCONSERVADORA_ID' => new sfValidatorChoice(array('choices' => array($this->getObject()->getUnidadconservadoraId()), 'empty_value' => $this->getObject()->getUnidadconservadoraId(), 'required' => false)),
      'DESCRIPCION'           => new sfValidatorString(array('max_length' => 250, 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('unidad_conservadora[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'UnidadConservadora';
  }


}
