<?php

/**
 * WfTipoDato form base class.
 *
 * @package    form
 * @subpackage wf_tipo_dato
 * @version    SVN: $Id: sfPropelFormGeneratedTemplate.php 15484 2009-02-13 13:13:51Z fabien $
 */
class BaseWfTipoDatoForm extends BaseFormPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'wftipodato_id' => new sfWidgetFormInputHidden(),
      'descripcion'   => new sfWidgetFormInputText(),
    ));

    $this->setValidators(array(
      'wftipodato_id' => new sfValidatorPropelChoice(array('model' => 'WfTipoDato', 'column' => 'wftipodato_id', 'required' => false)),
      'descripcion'   => new sfValidatorString(array('max_length' => 50, 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('wf_tipo_dato[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'WfTipoDato';
  }


}
