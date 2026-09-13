<?php

/**
 * WfEstado filter form base class.
 *
 * @package    simad
 * @subpackage filter
 * @author     Your name here
 */
abstract class BaseWfEstadoFormFilter extends BaseFormFilterPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'DESCRIPCION'  => new sfWidgetFormFilterInput(),
    ));

    $this->setValidators(array(
      'DESCRIPCION'  => new sfValidatorPass(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('wf_estado_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'WfEstado';
  }

  public function getFields()
  {
    return array(
      'WF_ESTADO_ID' => 'Number',
      'DESCRIPCION'  => 'Text',
    );
  }
}
