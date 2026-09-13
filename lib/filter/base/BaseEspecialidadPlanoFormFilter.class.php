<?php

/**
 * EspecialidadPlano filter form base class.
 *
 * @package    simad
 * @subpackage filter
 * @author     Your name here
 */
abstract class BaseEspecialidadPlanoFormFilter extends BaseFormFilterPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'DESCRIPCION'          => new sfWidgetFormFilterInput(),
    ));

    $this->setValidators(array(
      'DESCRIPCION'          => new sfValidatorPass(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('especialidad_plano_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'EspecialidadPlano';
  }

  public function getFields()
  {
    return array(
      'ESPECIALIDADPLANO_ID' => 'Number',
      'DESCRIPCION'          => 'Text',
    );
  }
}
