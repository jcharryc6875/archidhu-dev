<?php

/**
 * WfImagenInstancia filter form base class.
 *
 * @package    simad
 * @subpackage filter
 * @author     Your name here
 */
abstract class BaseWfImagenInstanciaFormFilter extends BaseFormFilterPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'wfinstancia_id'         => new sfWidgetFormPropelChoice(array('model' => 'WfInstancia', 'add_empty' => true)),
      'wf_estado_imagen_id'    => new sfWidgetFormPropelChoice(array('model' => 'WfEstadoImagen', 'add_empty' => true)),
      'descripcion'            => new sfWidgetFormFilterInput(),
      'ruta'                   => new sfWidgetFormFilterInput(),
      'fecha_creacion'         => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate())),
      'folios'                 => new sfWidgetFormFilterInput(),
      'fecha_documento'        => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate())),
    ));

    $this->setValidators(array(
      'wfinstancia_id'         => new sfValidatorPropelChoice(array('required' => false, 'model' => 'WfInstancia', 'column' => 'WFINSTANCIA_ID')),
      'wf_estado_imagen_id'    => new sfValidatorPropelChoice(array('required' => false, 'model' => 'WfEstadoImagen', 'column' => 'wf_estado_imagen_id')),
      'descripcion'            => new sfValidatorPass(array('required' => false)),
      'ruta'                   => new sfValidatorPass(array('required' => false)),
      'fecha_creacion'         => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDate(array('required' => false)), 'to_date' => new sfValidatorDate(array('required' => false)))),
      'folios'                 => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'fecha_documento'        => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDate(array('required' => false)), 'to_date' => new sfValidatorDate(array('required' => false)))),
    ));

    $this->widgetSchema->setNameFormat('wf_imagen_instancia_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'WfImagenInstancia';
  }

  public function getFields()
  {
    return array(
      'wf_imagen_instancia_id' => 'Number',
      'wfinstancia_id'         => 'ForeignKey',
      'wf_estado_imagen_id'    => 'ForeignKey',
      'descripcion'            => 'Text',
      'ruta'                   => 'Text',
      'fecha_creacion'         => 'Date',
      'folios'                 => 'Number',
      'fecha_documento'        => 'Date',
    );
  }
}
