<?php

/**
 * ProvUsuarioArea filter form base class.
 *
 * @package    simad
 * @subpackage filter
 * @author     Your name here
 */
abstract class BaseProvUsuarioAreaFormFilter extends BaseFormFilterPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'PROV_ESTADO_APROBADOR_ID' => new sfWidgetFormPropelChoice(array('model' => 'ProvEstadoAprobador', 'add_empty' => true)),
      'USUARIO_ID'               => new sfWidgetFormPropelChoice(array('model' => 'Usuario', 'add_empty' => true)),
      'PAIS_ID'                  => new sfWidgetFormPropelChoice(array('model' => 'Pais', 'add_empty' => true)),
      'PROV_AREA_APROBADORA_ID'  => new sfWidgetFormPropelChoice(array('model' => 'ProvAreaAprobadora', 'add_empty' => true)),
    ));

    $this->setValidators(array(
      'PROV_ESTADO_APROBADOR_ID' => new sfValidatorPropelChoice(array('required' => false, 'model' => 'ProvEstadoAprobador', 'column' => 'PROV_ESTADO_APROBADOR_ID')),
      'USUARIO_ID'               => new sfValidatorPropelChoice(array('required' => false, 'model' => 'Usuario', 'column' => 'USUARIO_ID')),
      'PAIS_ID'                  => new sfValidatorPropelChoice(array('required' => false, 'model' => 'Pais', 'column' => 'PAIS_ID')),
      'PROV_AREA_APROBADORA_ID'  => new sfValidatorPropelChoice(array('required' => false, 'model' => 'ProvAreaAprobadora', 'column' => 'PROV_AREA_APROBADORA_ID')),
    ));

    $this->widgetSchema->setNameFormat('prov_usuario_area_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'ProvUsuarioArea';
  }

  public function getFields()
  {
    return array(
      'PROV_USUARIO_AREA_ID'     => 'Number',
      'PROV_ESTADO_APROBADOR_ID' => 'ForeignKey',
      'USUARIO_ID'               => 'ForeignKey',
      'PAIS_ID'                  => 'ForeignKey',
      'PROV_AREA_APROBADORA_ID'  => 'ForeignKey',
    );
  }
}
