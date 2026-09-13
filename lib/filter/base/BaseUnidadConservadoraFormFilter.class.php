<?php

/**
 * UnidadConservadora filter form base class.
 *
 * @package    simad
 * @subpackage filter
 * @author     Your name here
 */
abstract class BaseUnidadConservadoraFormFilter extends BaseFormFilterPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'DESCRIPCION'           => new sfWidgetFormFilterInput(),
    ));

    $this->setValidators(array(
      'DESCRIPCION'           => new sfValidatorPass(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('unidad_conservadora_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'UnidadConservadora';
  }

  public function getFields()
  {
    return array(
      'UNIDADCONSERVADORA_ID' => 'Number',
      'DESCRIPCION'           => 'Text',
    );
  }
}
