<?php

/**
 * FactHistVitacora filter form base class.
 *
 * @package    simad
 * @subpackage filter
 * @author     Your name here
 */
abstract class BaseFactHistVitacoraFormFilter extends BaseFormFilterPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'FACTHIST_ID'         => new sfWidgetFormPropelChoice(array('model' => 'FactHist', 'add_empty' => true)),
      'ESTADO'              => new sfWidgetFormFilterInput(),
      'FECHA'               => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate())),
      'ORIGEN'              => new sfWidgetFormFilterInput(),
      'DESTINO'             => new sfWidgetFormFilterInput(),
      'ESTADO_ID'           => new sfWidgetFormFilterInput(),
      'ORIGEN_ID'           => new sfWidgetFormFilterInput(),
      'DESTINO_ID'          => new sfWidgetFormFilterInput(),
      'OBSERVACIONES'       => new sfWidgetFormFilterInput(),
    ));

    $this->setValidators(array(
      'FACTHIST_ID'         => new sfValidatorPropelChoice(array('required' => false, 'model' => 'FactHist', 'column' => 'FACTHIST_ID')),
      'ESTADO'              => new sfValidatorPass(array('required' => false)),
      'FECHA'               => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDate(array('required' => false)), 'to_date' => new sfValidatorDate(array('required' => false)))),
      'ORIGEN'              => new sfValidatorPass(array('required' => false)),
      'DESTINO'             => new sfValidatorPass(array('required' => false)),
      'ESTADO_ID'           => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'ORIGEN_ID'           => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'DESTINO_ID'          => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'OBSERVACIONES'       => new sfValidatorPass(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('fact_hist_vitacora_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'FactHistVitacora';
  }

  public function getFields()
  {
    return array(
      'FACTHISTVITACORA_ID' => 'Number',
      'FACTHIST_ID'         => 'ForeignKey',
      'ESTADO'              => 'Text',
      'FECHA'               => 'Date',
      'ORIGEN'              => 'Text',
      'DESTINO'             => 'Text',
      'ESTADO_ID'           => 'Number',
      'ORIGEN_ID'           => 'Number',
      'DESTINO_ID'          => 'Number',
      'OBSERVACIONES'       => 'Text',
    );
  }
}
