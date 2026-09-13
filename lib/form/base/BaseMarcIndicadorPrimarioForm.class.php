<?php

/**
 * MarcIndicadorPrimario form base class.
 *
 * @method MarcIndicadorPrimario getObject() Returns the current form's model object
 *
 * @package    ##PROJECT_NAME##
 * @subpackage form
 * @author     ##AUTHOR_NAME##
 */
abstract class BaseMarcIndicadorPrimarioForm extends BaseFormPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'MARCINDICADORPRIMARIO_ID' => new sfWidgetFormInputHidden(),
      'MARC_ID'                  => new sfWidgetFormPropelChoice(array('model' => 'Marc', 'add_empty' => false)),
      'CODIGO'                   => new sfWidgetFormInputText(),
      'DESCRIPCION'              => new sfWidgetFormInputText(),
    ));

    $this->setValidators(array(
      'MARCINDICADORPRIMARIO_ID' => new sfValidatorChoice(array('choices' => array($this->getObject()->getMarcindicadorprimarioId()), 'empty_value' => $this->getObject()->getMarcindicadorprimarioId(), 'required' => false)),
      'MARC_ID'                  => new sfValidatorPropelChoice(array('model' => 'Marc', 'column' => 'MARC_ID')),
      'CODIGO'                   => new sfValidatorString(array('max_length' => 50, 'required' => false)),
      'DESCRIPCION'              => new sfValidatorString(array('max_length' => 250, 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('marc_indicador_primario[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'MarcIndicadorPrimario';
  }


}
