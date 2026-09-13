<?php

/**
 * MarcVariableDoc filter form base class.
 *
 * @package    simad
 * @subpackage filter
 * @author     Your name here
 */
abstract class BaseMarcVariableDocFormFilter extends BaseFormFilterPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'MARCSUBCAMPO_ID'            => new sfWidgetFormPropelChoice(array('model' => 'MarcSubcampo', 'add_empty' => true)),
      'MARC_ID'                    => new sfWidgetFormPropelChoice(array('model' => 'Marc', 'add_empty' => true)),
      'DOCUMENTACION_ID'           => new sfWidgetFormPropelChoice(array('model' => 'Documentacion', 'add_empty' => true)),
      'MARCINDICADORPRIMARIO_ID'   => new sfWidgetFormPropelChoice(array('model' => 'MarcIndicadorPrimario', 'add_empty' => true)),
      'MARCINDICADORSECUNDARIO_ID' => new sfWidgetFormPropelChoice(array('model' => 'MarcIndicadorSecundario', 'add_empty' => true)),
      'VALOR_VARIABLE'             => new sfWidgetFormFilterInput(),
    ));

    $this->setValidators(array(
      'MARCSUBCAMPO_ID'            => new sfValidatorPropelChoice(array('required' => false, 'model' => 'MarcSubcampo', 'column' => 'MARCSUBCAMPO_ID')),
      'MARC_ID'                    => new sfValidatorPropelChoice(array('required' => false, 'model' => 'Marc', 'column' => 'MARC_ID')),
      'DOCUMENTACION_ID'           => new sfValidatorPropelChoice(array('required' => false, 'model' => 'Documentacion', 'column' => 'DOCUMENTACION_ID')),
      'MARCINDICADORPRIMARIO_ID'   => new sfValidatorPropelChoice(array('required' => false, 'model' => 'MarcIndicadorPrimario', 'column' => 'MARCINDICADORPRIMARIO_ID')),
      'MARCINDICADORSECUNDARIO_ID' => new sfValidatorPropelChoice(array('required' => false, 'model' => 'MarcIndicadorSecundario', 'column' => 'MARCINDICADORSECUNDARIO_ID')),
      'VALOR_VARIABLE'             => new sfValidatorPass(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('marc_variable_doc_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'MarcVariableDoc';
  }

  public function getFields()
  {
    return array(
      'MARCVARIABLEDOC_ID'         => 'Number',
      'MARCSUBCAMPO_ID'            => 'ForeignKey',
      'MARC_ID'                    => 'ForeignKey',
      'DOCUMENTACION_ID'           => 'ForeignKey',
      'MARCINDICADORPRIMARIO_ID'   => 'ForeignKey',
      'MARCINDICADORSECUNDARIO_ID' => 'ForeignKey',
      'VALOR_VARIABLE'             => 'Text',
    );
  }
}
