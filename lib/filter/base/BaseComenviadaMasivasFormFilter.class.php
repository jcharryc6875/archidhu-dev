<?php

/**
 * ComenviadaMasivas filter form base class.
 *
 * @package    simad
 * @subpackage filter
 * @author     Your name here
 */
abstract class BaseComenviadaMasivasFormFilter extends BaseFormFilterPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'USUARIO_ID'           => new sfWidgetFormPropelChoice(array('model' => 'Usuario', 'add_empty' => true)),
      'COMRECIBIDA_ID'       => new sfWidgetFormPropelChoice(array('model' => 'ComRecibida', 'add_empty' => true)),
      'COMENVIADA_ID'        => new sfWidgetFormPropelChoice(array('model' => 'ComEnviada', 'add_empty' => true)),
      'PLANTILLASCOM_ID'     => new sfWidgetFormPropelChoice(array('model' => 'PlantillasCom', 'add_empty' => true)),
      'MARCA'                => new sfWidgetFormFilterInput(),
      'ESTADO_PROCESO'       => new sfWidgetFormFilterInput(),
      'MSG_PROCESO'          => new sfWidgetFormFilterInput(),
      'USUARIOS_FIRMAS'      => new sfWidgetFormFilterInput(),
      'CARGOS_FIRMAS'        => new sfWidgetFormFilterInput(),
      'FECHA_CREACION'       => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate())),
      'FECHA_EJECUCION'      => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate())),
      'CONTENIDO_TEXT'       => new sfWidgetFormFilterInput(),
      'SEND_EMAIL'           => new sfWidgetFormFilterInput(),
      'GENERATE_FILE'        => new sfWidgetFormFilterInput(),
      'RADICADO_RESPUESTA'   => new sfWidgetFormFilterInput(),
    ));

    $this->setValidators(array(
      'USUARIO_ID'           => new sfValidatorPropelChoice(array('required' => false, 'model' => 'Usuario', 'column' => 'USUARIO_ID')),
      'COMRECIBIDA_ID'       => new sfValidatorPropelChoice(array('required' => false, 'model' => 'ComRecibida', 'column' => 'COMRECIBIDA_ID')),
      'COMENVIADA_ID'        => new sfValidatorPropelChoice(array('required' => false, 'model' => 'ComEnviada', 'column' => 'COMENVIADA_ID')),
      'PLANTILLASCOM_ID'     => new sfValidatorPropelChoice(array('required' => false, 'model' => 'PlantillasCom', 'column' => 'PLANTILLASCOM_ID')),
      'MARCA'                => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'ESTADO_PROCESO'       => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'MSG_PROCESO'          => new sfValidatorPass(array('required' => false)),
      'USUARIOS_FIRMAS'      => new sfValidatorPass(array('required' => false)),
      'CARGOS_FIRMAS'        => new sfValidatorPass(array('required' => false)),
      'FECHA_CREACION'       => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDate(array('required' => false)), 'to_date' => new sfValidatorDate(array('required' => false)))),
      'FECHA_EJECUCION'      => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDate(array('required' => false)), 'to_date' => new sfValidatorDate(array('required' => false)))),
      'CONTENIDO_TEXT'       => new sfValidatorPass(array('required' => false)),
      'SEND_EMAIL'           => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'GENERATE_FILE'        => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'RADICADO_RESPUESTA'   => new sfValidatorPass(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('comenviada_masivas_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'ComenviadaMasivas';
  }

  public function getFields()
  {
    return array(
      'COMENVIADAMASIVAS_ID' => 'Number',
      'USUARIO_ID'           => 'ForeignKey',
      'COMRECIBIDA_ID'       => 'ForeignKey',
      'COMENVIADA_ID'        => 'ForeignKey',
      'PLANTILLASCOM_ID'     => 'ForeignKey',
      'MARCA'                => 'Number',
      'ESTADO_PROCESO'       => 'Number',
      'MSG_PROCESO'          => 'Text',
      'USUARIOS_FIRMAS'      => 'Text',
      'CARGOS_FIRMAS'        => 'Text',
      'FECHA_CREACION'       => 'Date',
      'FECHA_EJECUCION'      => 'Date',
      'CONTENIDO_TEXT'       => 'Text',
      'SEND_EMAIL'           => 'Number',
      'GENERATE_FILE'        => 'Number',
      'RADICADO_RESPUESTA'   => 'Text',
    );
  }
}
