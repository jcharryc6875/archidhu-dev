<?php

/**
 * MarcIndicadorSecundario filter form base class.
 *
 * @package    simad
 * @subpackage filter
 * @author     Your name here
 */
abstract class BaseMarcIndicadorSecundarioFormFilter extends BaseFormFilterPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'MARC_ID'                    => new sfWidgetFormPropelChoice(array('model' => 'Marc', 'add_empty' => true)),
      'CODIGO'                     => new sfWidgetFormFilterInput(),
      'DESCRIPCION'                => new sfWidgetFormFilterInput(),
    ));

    $this->setValidators(array(
      'MARC_ID'                    => new sfValidatorPropelChoice(array('required' => false, 'model' => 'Marc', 'column' => 'MARC_ID')),
      'CODIGO'                     => new sfValidatorPass(array('required' => false)),
      'DESCRIPCION'                => new sfValidatorPass(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('marc_indicador_secundario_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'MarcIndicadorSecundario';
  }

  public function getFields()
  {
    return array(
      'MARCINDICADORSECUNDARIO_ID' => 'Number',
      'MARC_ID'                    => 'ForeignKey',
      'CODIGO'                     => 'Text',
      'DESCRIPCION'                => 'Text',
    );
  }
}
