<?php

/**
 * ProvCheckListVigencia form base class.
 *
 * @method ProvCheckListVigencia getObject() Returns the current form's model object
 *
 * @package    ##PROJECT_NAME##
 * @subpackage form
 * @author     ##AUTHOR_NAME##
 */
abstract class BaseProvCheckListVigenciaForm extends BaseFormPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'PROV_CHECK_LIST_VIGENCIA_ID' => new sfWidgetFormInputHidden(),
      'PROV_PERIODO_VALIDEZ_ID'     => new sfWidgetFormPropelChoice(array('model' => 'ProvPeriodoValidez', 'add_empty' => false)),
      'PROV_CHECK_LIST_PREGUNTA_ID' => new sfWidgetFormPropelChoice(array('model' => 'ProvCheckListPregunta', 'add_empty' => false)),
      'DESCRIPCION'                 => new sfWidgetFormInputText(),
      'RESPUESTA'                   => new sfWidgetFormInputText(),
      'OBSERVACIONES'               => new sfWidgetFormInputText(),
    ));

    $this->setValidators(array(
      'PROV_CHECK_LIST_VIGENCIA_ID' => new sfValidatorChoice(array('choices' => array($this->getObject()->getProvCheckListVigenciaId()), 'empty_value' => $this->getObject()->getProvCheckListVigenciaId(), 'required' => false)),
      'PROV_PERIODO_VALIDEZ_ID'     => new sfValidatorPropelChoice(array('model' => 'ProvPeriodoValidez', 'column' => 'PROV_PERIODO_VALIDEZ_ID')),
      'PROV_CHECK_LIST_PREGUNTA_ID' => new sfValidatorPropelChoice(array('model' => 'ProvCheckListPregunta', 'column' => 'PROV_CHECK_LIST_PREGUNTA_ID')),
      'DESCRIPCION'                 => new sfValidatorString(array('max_length' => 250, 'required' => false)),
      'RESPUESTA'                   => new sfValidatorString(array('max_length' => 50, 'required' => false)),
      'OBSERVACIONES'               => new sfValidatorString(array('max_length' => 200, 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('prov_check_list_vigencia[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'ProvCheckListVigencia';
  }


}
