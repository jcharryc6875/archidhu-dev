<?php

/**
 * MarcVariableDoc form base class.
 *
 * @method MarcVariableDoc getObject() Returns the current form's model object
 *
 * @package    ##PROJECT_NAME##
 * @subpackage form
 * @author     ##AUTHOR_NAME##
 */
abstract class BaseMarcVariableDocForm extends BaseFormPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'MARCVARIABLEDOC_ID'         => new sfWidgetFormInputHidden(),
      'MARCSUBCAMPO_ID'            => new sfWidgetFormPropelChoice(array('model' => 'MarcSubcampo', 'add_empty' => false)),
      'MARC_ID'                    => new sfWidgetFormPropelChoice(array('model' => 'Marc', 'add_empty' => false)),
      'DOCUMENTACION_ID'           => new sfWidgetFormPropelChoice(array('model' => 'Documentacion', 'add_empty' => false)),
      'MARCINDICADORPRIMARIO_ID'   => new sfWidgetFormPropelChoice(array('model' => 'MarcIndicadorPrimario', 'add_empty' => true)),
      'MARCINDICADORSECUNDARIO_ID' => new sfWidgetFormPropelChoice(array('model' => 'MarcIndicadorSecundario', 'add_empty' => true)),
      'VALOR_VARIABLE'             => new sfWidgetFormInputText(),
    ));

    $this->setValidators(array(
      'MARCVARIABLEDOC_ID'         => new sfValidatorChoice(array('choices' => array($this->getObject()->getMarcvariabledocId()), 'empty_value' => $this->getObject()->getMarcvariabledocId(), 'required' => false)),
      'MARCSUBCAMPO_ID'            => new sfValidatorPropelChoice(array('model' => 'MarcSubcampo', 'column' => 'MARCSUBCAMPO_ID')),
      'MARC_ID'                    => new sfValidatorPropelChoice(array('model' => 'Marc', 'column' => 'MARC_ID')),
      'DOCUMENTACION_ID'           => new sfValidatorPropelChoice(array('model' => 'Documentacion', 'column' => 'DOCUMENTACION_ID')),
      'MARCINDICADORPRIMARIO_ID'   => new sfValidatorPropelChoice(array('model' => 'MarcIndicadorPrimario', 'column' => 'MARCINDICADORPRIMARIO_ID', 'required' => false)),
      'MARCINDICADORSECUNDARIO_ID' => new sfValidatorPropelChoice(array('model' => 'MarcIndicadorSecundario', 'column' => 'MARCINDICADORSECUNDARIO_ID', 'required' => false)),
      'VALOR_VARIABLE'             => new sfValidatorString(array('max_length' => 500, 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('marc_variable_doc[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'MarcVariableDoc';
  }


}
