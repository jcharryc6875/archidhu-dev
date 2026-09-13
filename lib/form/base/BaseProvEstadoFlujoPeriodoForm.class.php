<?php

/**
 * ProvEstadoFlujoPeriodo form base class.
 *
 * @method ProvEstadoFlujoPeriodo getObject() Returns the current form's model object
 *
 * @package    ##PROJECT_NAME##
 * @subpackage form
 * @author     ##AUTHOR_NAME##
 */
abstract class BaseProvEstadoFlujoPeriodoForm extends BaseFormPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'PROV_ESTADO_FLUJO_PERIODO_ID' => new sfWidgetFormInputHidden(),
      'PROV_ESTADO_APROBACION_ID'    => new sfWidgetFormPropelChoice(array('model' => 'ProvEstadoAprobacion', 'add_empty' => false)),
      'PROV_PERIODO_VALIDEZ_ID'      => new sfWidgetFormPropelChoice(array('model' => 'ProvPeriodoValidez', 'add_empty' => false)),
      'PROV_ITEM_FLUJO_ID'           => new sfWidgetFormPropelChoice(array('model' => 'ProvItemFlujo', 'add_empty' => false)),
      'DESCRIPCION'                  => new sfWidgetFormInputText(),
    ));

    $this->setValidators(array(
      'PROV_ESTADO_FLUJO_PERIODO_ID' => new sfValidatorChoice(array('choices' => array($this->getObject()->getProvEstadoFlujoPeriodoId()), 'empty_value' => $this->getObject()->getProvEstadoFlujoPeriodoId(), 'required' => false)),
      'PROV_ESTADO_APROBACION_ID'    => new sfValidatorPropelChoice(array('model' => 'ProvEstadoAprobacion', 'column' => 'PROV_ESTADO_APROBACION_ID')),
      'PROV_PERIODO_VALIDEZ_ID'      => new sfValidatorPropelChoice(array('model' => 'ProvPeriodoValidez', 'column' => 'PROV_PERIODO_VALIDEZ_ID')),
      'PROV_ITEM_FLUJO_ID'           => new sfValidatorPropelChoice(array('model' => 'ProvItemFlujo', 'column' => 'PROV_ITEM_FLUJO_ID')),
      'DESCRIPCION'                  => new sfValidatorString(array('max_length' => 250, 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('prov_estado_flujo_periodo[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'ProvEstadoFlujoPeriodo';
  }


}
