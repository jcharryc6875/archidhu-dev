<?php

/**
 * Vinculada filter form base class.
 *
 * @package    simad
 * @subpackage filter
 * @author     Your name here
 */
abstract class BaseVinculadaFormFilter extends BaseFormFilterPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'USUARIO_ID'     => new sfWidgetFormPropelChoice(array('model' => 'Usuario', 'add_empty' => true)),
      'DESCRIPCION'    => new sfWidgetFormFilterInput(),
      'RUTA'           => new sfWidgetFormFilterInput(),
      'FECHA_CREACION' => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate())),
      'FOLIOS'         => new sfWidgetFormFilterInput(),
      'ACEPTADO'       => new sfWidgetFormFilterInput(),
    ));

    $this->setValidators(array(
      'USUARIO_ID'     => new sfValidatorPropelChoice(array('required' => false, 'model' => 'Usuario', 'column' => 'USUARIO_ID')),
      'DESCRIPCION'    => new sfValidatorPass(array('required' => false)),
      'RUTA'           => new sfValidatorPass(array('required' => false)),
      'FECHA_CREACION' => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDate(array('required' => false)), 'to_date' => new sfValidatorDate(array('required' => false)))),
      'FOLIOS'         => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'ACEPTADO'       => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
    ));

    $this->widgetSchema->setNameFormat('vinculada_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'Vinculada';
  }

  public function getFields()
  {
    return array(
      'VINCULADA_ID'   => 'Number',
      'USUARIO_ID'     => 'ForeignKey',
      'DESCRIPCION'    => 'Text',
      'RUTA'           => 'Text',
      'FECHA_CREACION' => 'Date',
      'FOLIOS'         => 'Number',
      'ACEPTADO'       => 'Number',
    );
  }
}
