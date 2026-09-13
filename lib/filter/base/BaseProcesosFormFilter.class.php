<?php

/**
 * Procesos filter form base class.
 *
 * @package    simad
 * @subpackage filter
 * @author     Your name here
 */
abstract class BaseProcesosFormFilter extends BaseFormFilterPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'MACROPROCESO_ID' => new sfWidgetFormPropelChoice(array('model' => 'MacroProceso', 'add_empty' => true)),
      'DESCRIPCION'     => new sfWidgetFormFilterInput(),
      'CODIGO'          => new sfWidgetFormFilterInput(),
    ));

    $this->setValidators(array(
      'MACROPROCESO_ID' => new sfValidatorPropelChoice(array('required' => false, 'model' => 'MacroProceso', 'column' => 'MACROPROCESO_ID')),
      'DESCRIPCION'     => new sfValidatorPass(array('required' => false)),
      'CODIGO'          => new sfValidatorPass(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('procesos_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'Procesos';
  }

  public function getFields()
  {
    return array(
      'PROCESOS_ID'     => 'Number',
      'MACROPROCESO_ID' => 'ForeignKey',
      'DESCRIPCION'     => 'Text',
      'CODIGO'          => 'Text',
    );
  }
}
