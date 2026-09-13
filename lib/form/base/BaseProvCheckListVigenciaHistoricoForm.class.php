<?php

/**
 * ProvCheckListVigenciaHistorico form base class.
 *
 * @method ProvCheckListVigenciaHistorico getObject() Returns the current form's model object
 *
 * @package    ##PROJECT_NAME##
 * @subpackage form
 * @author     ##AUTHOR_NAME##
 */
abstract class BaseProvCheckListVigenciaHistoricoForm extends BaseFormPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'PROV_CHECK_LIST_VIGENCIA_HISTORICO_ID' => new sfWidgetFormInputHidden(),
      'PROV_CHECK_LIST_VIGENCIA_ID'           => new sfWidgetFormPropelChoice(array('model' => 'ProvCheckListVigencia', 'add_empty' => false)),
      'USUARIO_ID'                            => new sfWidgetFormPropelChoice(array('model' => 'Usuario', 'add_empty' => false)),
      'DESCRIPCION'                           => new sfWidgetFormInputText(),
      'RESPUESTA'                             => new sfWidgetFormInputText(),
      'OBSERVACIONES'                         => new sfWidgetFormInputText(),
      'FECHA_CREACION'                        => new sfWidgetFormDateTime(),
    ));

    $this->setValidators(array(
      'PROV_CHECK_LIST_VIGENCIA_HISTORICO_ID' => new sfValidatorChoice(array('choices' => array($this->getObject()->getProvCheckListVigenciaHistoricoId()), 'empty_value' => $this->getObject()->getProvCheckListVigenciaHistoricoId(), 'required' => false)),
      'PROV_CHECK_LIST_VIGENCIA_ID'           => new sfValidatorPropelChoice(array('model' => 'ProvCheckListVigencia', 'column' => 'PROV_CHECK_LIST_VIGENCIA_ID')),
      'USUARIO_ID'                            => new sfValidatorPropelChoice(array('model' => 'Usuario', 'column' => 'USUARIO_ID')),
      'DESCRIPCION'                           => new sfValidatorString(array('max_length' => 250, 'required' => false)),
      'RESPUESTA'                             => new sfValidatorString(array('max_length' => 50, 'required' => false)),
      'OBSERVACIONES'                         => new sfValidatorString(array('max_length' => 200, 'required' => false)),
      'FECHA_CREACION'                        => new sfValidatorDateTime(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('prov_check_list_vigencia_historico[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'ProvCheckListVigenciaHistorico';
  }


}
