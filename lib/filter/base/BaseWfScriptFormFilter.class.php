<?php

/**
 * WfScript filter form base class.
 *
 * @package    simad
 * @subpackage filter
 * @author     Your name here
 */
abstract class BaseWfScriptFormFilter extends BaseFormFilterPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'DESCRIPCION' => new sfWidgetFormFilterInput(),
      'CONTENIDO'   => new sfWidgetFormFilterInput(),
    ));

    $this->setValidators(array(
      'DESCRIPCION' => new sfValidatorPass(array('required' => false)),
      'CONTENIDO'   => new sfValidatorPass(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('wf_script_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'WfScript';
  }

  public function getFields()
  {
    return array(
      'WFSCRIPT_ID' => 'Number',
      'DESCRIPCION' => 'Text',
      'CONTENIDO'   => 'Text',
    );
  }
}
