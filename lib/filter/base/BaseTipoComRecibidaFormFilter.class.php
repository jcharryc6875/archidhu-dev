<?php

/**
 * TipoComRecibida filter form base class.
 *
 * @package    simad
 * @subpackage filter
 * @author     Your name here
 */
abstract class BaseTipoComRecibidaFormFilter extends BaseFormFilterPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'DESCRIPCION'         => new sfWidgetFormFilterInput(),
      'DIAS_RESPUESTA'      => new sfWidgetFormFilterInput(),
      'ES_ACCION_LEGAL'     => new sfWidgetFormFilterInput(),
      'TIPOFIRMADIGITAL_ID' => new sfWidgetFormFilterInput(),
      'TIPODISTRIBUCION_ID' => new sfWidgetFormPropelChoice(array('model' => 'TipoDistribucion', 'add_empty' => true)),
      'ES_VISIBLE'          => new sfWidgetFormFilterInput(),
      'DIAS_HABILES'        => new sfWidgetFormFilterInput(),
    ));

    $this->setValidators(array(
      'DESCRIPCION'         => new sfValidatorPass(array('required' => false)),
      'DIAS_RESPUESTA'      => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'ES_ACCION_LEGAL'     => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'TIPOFIRMADIGITAL_ID' => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'TIPODISTRIBUCION_ID' => new sfValidatorPropelChoice(array('required' => false, 'model' => 'TipoDistribucion', 'column' => 'TIPODISTRIBUCION_ID')),
      'ES_VISIBLE'          => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'DIAS_HABILES'        => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
    ));

    $this->widgetSchema->setNameFormat('tipo_com_recibida_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'TipoComRecibida';
  }

  public function getFields()
  {
    return array(
      'TIPOCOMRECIBIDA_ID'  => 'Number',
      'DESCRIPCION'         => 'Text',
      'DIAS_RESPUESTA'      => 'Number',
      'ES_ACCION_LEGAL'     => 'Number',
      'TIPOFIRMADIGITAL_ID' => 'Number',
      'TIPODISTRIBUCION_ID' => 'ForeignKey',
      'ES_VISIBLE'          => 'Number',
      'DIAS_HABILES'        => 'Number',
    );
  }
}
