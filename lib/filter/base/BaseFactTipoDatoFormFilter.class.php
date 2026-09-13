<?php

/**
 * FactTipoDato filter form base class.
 *
 * @package    simad
 * @subpackage filter
 * @author     Your name here
 */
abstract class BaseFactTipoDatoFormFilter extends BaseFormFilterPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'DESCRIPCION'     => new sfWidgetFormFilterInput(),
    ));

    $this->setValidators(array(
      'DESCRIPCION'     => new sfValidatorPass(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('fact_tipo_dato_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'FactTipoDato';
  }

  public function getFields()
  {
    return array(
      'FACTTIPODATO_ID' => 'Number',
      'DESCRIPCION'     => 'Text',
    );
  }
}
