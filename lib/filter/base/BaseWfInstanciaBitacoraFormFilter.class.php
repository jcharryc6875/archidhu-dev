<?php

/**
 * WfInstanciaBitacora filter form base class.
 *
 * @package    simad
 * @subpackage filter
 * @author     Your name here
 */
abstract class BaseWfInstanciaBitacoraFormFilter extends BaseFormFilterPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'USUARIO_ID'               => new sfWidgetFormPropelChoice(array('model' => 'Usuario', 'add_empty' => true)),
      'COMENVIADA_ID'            => new sfWidgetFormPropelChoice(array('model' => 'ComEnviada', 'add_empty' => true)),
      'COMINTERNA_ID'            => new sfWidgetFormPropelChoice(array('model' => 'ComInterna', 'add_empty' => true)),
      'WFINSTANCIA_ID'           => new sfWidgetFormPropelChoice(array('model' => 'WfInstancia', 'add_empty' => true)),
      'WFACTIVIDADTRANSICION_ID' => new sfWidgetFormPropelChoice(array('model' => 'WfActividadTransicion', 'add_empty' => true)),
      'COMRECIBIDA_ID'           => new sfWidgetFormPropelChoice(array('model' => 'ComRecibida', 'add_empty' => true)),
      'FECHA_I'                  => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate())),
      'FECHA_F'                  => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate())),
      'OBSERVACIONES'            => new sfWidgetFormFilterInput(),
      'ERROR'                    => new sfWidgetFormFilterInput(),
      'ES_ACTUAL'                => new sfWidgetFormFilterInput(),
      'FECHA_LIMITE'             => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate())),
      'BUZON'                    => new sfWidgetFormFilterInput(),
      'BUZON_ID'                 => new sfWidgetFormFilterInput(),
      'ESTADO_ACTIVIDAD'         => new sfWidgetFormFilterInput(),
      'SCRIPT'                   => new sfWidgetFormFilterInput(),
      'SCRIPT_PARAMS'            => new sfWidgetFormFilterInput(),
      'VALOR_VARIABLES'          => new sfWidgetFormFilterInput(),
      'WF_NOMBRE_VARIABLES'      => new sfWidgetFormFilterInput(),
    ));

    $this->setValidators(array(
      'USUARIO_ID'               => new sfValidatorPropelChoice(array('required' => false, 'model' => 'Usuario', 'column' => 'USUARIO_ID')),
      'COMENVIADA_ID'            => new sfValidatorPropelChoice(array('required' => false, 'model' => 'ComEnviada', 'column' => 'COMENVIADA_ID')),
      'COMINTERNA_ID'            => new sfValidatorPropelChoice(array('required' => false, 'model' => 'ComInterna', 'column' => 'COMINTERNA_ID')),
      'WFINSTANCIA_ID'           => new sfValidatorPropelChoice(array('required' => false, 'model' => 'WfInstancia', 'column' => 'WFINSTANCIA_ID')),
      'WFACTIVIDADTRANSICION_ID' => new sfValidatorPropelChoice(array('required' => false, 'model' => 'WfActividadTransicion', 'column' => 'WFACTIVIDADTRANSICION_ID')),
      'COMRECIBIDA_ID'           => new sfValidatorPropelChoice(array('required' => false, 'model' => 'ComRecibida', 'column' => 'COMRECIBIDA_ID')),
      'FECHA_I'                  => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDate(array('required' => false)), 'to_date' => new sfValidatorDate(array('required' => false)))),
      'FECHA_F'                  => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDate(array('required' => false)), 'to_date' => new sfValidatorDate(array('required' => false)))),
      'OBSERVACIONES'            => new sfValidatorPass(array('required' => false)),
      'ERROR'                    => new sfValidatorPass(array('required' => false)),
      'ES_ACTUAL'                => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'FECHA_LIMITE'             => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDate(array('required' => false)), 'to_date' => new sfValidatorDate(array('required' => false)))),
      'BUZON'                    => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'BUZON_ID'                 => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'ESTADO_ACTIVIDAD'         => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'SCRIPT'                   => new sfValidatorPass(array('required' => false)),
      'SCRIPT_PARAMS'            => new sfValidatorPass(array('required' => false)),
      'VALOR_VARIABLES'          => new sfValidatorPass(array('required' => false)),
      'WF_NOMBRE_VARIABLES'      => new sfValidatorPass(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('wf_instancia_bitacora_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'WfInstanciaBitacora';
  }

  public function getFields()
  {
    return array(
      'WFINSTANCIABITACORA_ID'   => 'Number',
      'USUARIO_ID'               => 'ForeignKey',
      'COMENVIADA_ID'            => 'ForeignKey',
      'COMINTERNA_ID'            => 'ForeignKey',
      'WFINSTANCIA_ID'           => 'ForeignKey',
      'WFACTIVIDADTRANSICION_ID' => 'ForeignKey',
      'COMRECIBIDA_ID'           => 'ForeignKey',
      'FECHA_I'                  => 'Date',
      'FECHA_F'                  => 'Date',
      'OBSERVACIONES'            => 'Text',
      'ERROR'                    => 'Text',
      'ES_ACTUAL'                => 'Number',
      'FECHA_LIMITE'             => 'Date',
      'BUZON'                    => 'Number',
      'BUZON_ID'                 => 'Number',
      'ESTADO_ACTIVIDAD'         => 'Number',
      'SCRIPT'                   => 'Text',
      'SCRIPT_PARAMS'            => 'Text',
      'VALOR_VARIABLES'          => 'Text',
      'WF_NOMBRE_VARIABLES'      => 'Text',
    );
  }
}
