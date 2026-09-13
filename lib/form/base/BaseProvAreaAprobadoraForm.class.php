<?php

/**
 * ProvAreaAprobadora form base class.
 *
 * @method ProvAreaAprobadora getObject() Returns the current form's model object
 *
 * @package    ##PROJECT_NAME##
 * @subpackage form
 * @author     ##AUTHOR_NAME##
 */
abstract class BaseProvAreaAprobadoraForm extends BaseFormPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'PROV_AREA_APROBADORA_ID' => new sfWidgetFormInputHidden(),
      'NOMBRE'                  => new sfWidgetFormInputText(),
      'DESCRIPCION'             => new sfWidgetFormInputText(),
      'CODIGO'                  => new sfWidgetFormInputText(),
    ));

    $this->setValidators(array(
      'PROV_AREA_APROBADORA_ID' => new sfValidatorChoice(array('choices' => array($this->getObject()->getProvAreaAprobadoraId()), 'empty_value' => $this->getObject()->getProvAreaAprobadoraId(), 'required' => false)),
      'NOMBRE'                  => new sfValidatorString(array('max_length' => 100, 'required' => false)),
      'DESCRIPCION'             => new sfValidatorString(array('max_length' => 250, 'required' => false)),
      'CODIGO'                  => new sfValidatorString(array('max_length' => 50, 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('prov_area_aprobadora[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'ProvAreaAprobadora';
  }


}
