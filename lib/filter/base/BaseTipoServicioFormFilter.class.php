<?php

/**
 * TipoServicio filter form base class.
 *
 * @package    simad
 * @subpackage filter
 * @author     Your name here
 */
abstract class BaseTipoServicioFormFilter extends BaseFormFilterPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'ENTIDAD_ID'      => new sfWidgetFormPropelChoice(array('model' => 'Entidad', 'add_empty' => true)),
      'DESCRIPCION'     => new sfWidgetFormFilterInput(),
      'ES_VISIBLE'      => new sfWidgetFormFilterInput(),
    ));

    $this->setValidators(array(
      'ENTIDAD_ID'      => new sfValidatorPropelChoice(array('required' => false, 'model' => 'Entidad', 'column' => 'ENTIDAD_ID')),
      'DESCRIPCION'     => new sfValidatorPass(array('required' => false)),
      'ES_VISIBLE'      => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
    ));

    $this->widgetSchema->setNameFormat('tipo_servicio_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'TipoServicio';
  }

  public function getFields()
  {
    return array(
      'TIPOSERVICIO_ID' => 'Number',
      'ENTIDAD_ID'      => 'ForeignKey',
      'DESCRIPCION'     => 'Text',
      'ES_VISIBLE'      => 'Number',
    );
  }
}
