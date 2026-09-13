<?php

/**
 * WfFlujo form base class.
 *
 * @package    form
 * @subpackage wf_flujo
 * @version    SVN: $Id: sfPropelFormGeneratedTemplate.php 15484 2009-02-13 13:13:51Z fabien $
 */
class BaseWfFlujoForm extends BaseFormPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'wf_flujo_id'        => new sfWidgetFormInputHidden(),
      'tipocomrecibida_id' => new sfWidgetFormPropelSelect(array('model' => 'TipoComRecibida', 'add_empty' => true)),
      'tipocominterna_id'  => new sfWidgetFormPropelSelect(array('model' => 'TipoComInterna', 'add_empty' => true)),
      'wf_buzon_id'        => new sfWidgetFormPropelSelect(array('model' => 'WfBuzon', 'add_empty' => false)),
      'descripcion'        => new sfWidgetFormInputText(),
    ));

    $this->setValidators(array(
      'wf_flujo_id'        => new sfValidatorPropelChoice(array('model' => 'WfFlujo', 'column' => 'wf_flujo_id', 'required' => false)),
      'tipocomrecibida_id' => new sfValidatorPropelChoice(array('model' => 'TipoComRecibida', 'column' => 'tipocomrecibida_id', 'required' => false)),
      'tipocominterna_id'  => new sfValidatorPropelChoice(array('model' => 'TipoComInterna', 'column' => 'tipocominterna_id', 'required' => false)),
      'wf_buzon_id'        => new sfValidatorPropelChoice(array('model' => 'WfBuzon', 'column' => 'wf_buzon_id')),
      'descripcion'        => new sfValidatorString(array('max_length' => 250, 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('wf_flujo[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'WfFlujo';
  }


}
