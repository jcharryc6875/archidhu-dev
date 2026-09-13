<?php

/**
 * Procedimiento filter form base class.
 *
 * @package    simad
 * @subpackage filter
 * @author     Your name here
 */
abstract class BaseProcedimientoFormFilter extends BaseFormFilterPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'USUARIO_ID'           => new sfWidgetFormPropelChoice(array('model' => 'Usuario', 'add_empty' => true)),
      'TIPOPROCEDIMIENTO_ID' => new sfWidgetFormPropelChoice(array('model' => 'TipoProcedimiento', 'add_empty' => true)),
      'DEPENDENCIA_ID'       => new sfWidgetFormPropelChoice(array('model' => 'Dependencia', 'add_empty' => true)),
      'CODIGO'               => new sfWidgetFormFilterInput(),
      'NOMBRE'               => new sfWidgetFormFilterInput(),
      'DESCRIPCION'          => new sfWidgetFormFilterInput(),
      'EXTENSION'            => new sfWidgetFormFilterInput(),
      'RUTA'                 => new sfWidgetFormFilterInput(),
      'VERSION'              => new sfWidgetFormFilterInput(),
      'FECHA_CREACION'       => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate())),
      'CODIGO_VERSION'       => new sfWidgetFormFilterInput(),
      'ES_ULTIMA_VERSION'    => new sfWidgetFormFilterInput(),
      'TEXT_CAMBIOS'         => new sfWidgetFormFilterInput(),
      'ESTADO_APROBACION'    => new sfWidgetFormFilterInput(),
    ));

    $this->setValidators(array(
      'USUARIO_ID'           => new sfValidatorPropelChoice(array('required' => false, 'model' => 'Usuario', 'column' => 'USUARIO_ID')),
      'TIPOPROCEDIMIENTO_ID' => new sfValidatorPropelChoice(array('required' => false, 'model' => 'TipoProcedimiento', 'column' => 'TIPOPROCEDIMIENTO_ID')),
      'DEPENDENCIA_ID'       => new sfValidatorPropelChoice(array('required' => false, 'model' => 'Dependencia', 'column' => 'DEPENDENCIA_ID')),
      'CODIGO'               => new sfValidatorPass(array('required' => false)),
      'NOMBRE'               => new sfValidatorPass(array('required' => false)),
      'DESCRIPCION'          => new sfValidatorPass(array('required' => false)),
      'EXTENSION'            => new sfValidatorPass(array('required' => false)),
      'RUTA'                 => new sfValidatorPass(array('required' => false)),
      'VERSION'              => new sfValidatorSchemaFilter('text', new sfValidatorNumber(array('required' => false))),
      'FECHA_CREACION'       => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDate(array('required' => false)), 'to_date' => new sfValidatorDate(array('required' => false)))),
      'CODIGO_VERSION'       => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'ES_ULTIMA_VERSION'    => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'TEXT_CAMBIOS'         => new sfValidatorPass(array('required' => false)),
      'ESTADO_APROBACION'    => new sfValidatorPass(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('procedimiento_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'Procedimiento';
  }

  public function getFields()
  {
    return array(
      'PROCEDIMIENTO_ID'     => 'Number',
      'USUARIO_ID'           => 'ForeignKey',
      'TIPOPROCEDIMIENTO_ID' => 'ForeignKey',
      'DEPENDENCIA_ID'       => 'ForeignKey',
      'CODIGO'               => 'Text',
      'NOMBRE'               => 'Text',
      'DESCRIPCION'          => 'Text',
      'EXTENSION'            => 'Text',
      'RUTA'                 => 'Text',
      'VERSION'              => 'Number',
      'FECHA_CREACION'       => 'Date',
      'CODIGO_VERSION'       => 'Number',
      'ES_ULTIMA_VERSION'    => 'Number',
      'TEXT_CAMBIOS'         => 'Text',
      'ESTADO_APROBACION'    => 'Text',
    );
  }
}
