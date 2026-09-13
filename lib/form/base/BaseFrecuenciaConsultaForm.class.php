<?php

/**
 * FrecuenciaConsulta form base class.
 *
 * @method FrecuenciaConsulta getObject() Returns the current form's model object
 *
 * @package    ##PROJECT_NAME##
 * @subpackage form
 * @author     ##AUTHOR_NAME##
 */
abstract class BaseFrecuenciaConsultaForm extends BaseFormPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'FRECUENCIACONSULTA_ID' => new sfWidgetFormInputHidden(),
      'DESCRIPCION'           => new sfWidgetFormInputText(),
    ));

    $this->setValidators(array(
      'FRECUENCIACONSULTA_ID' => new sfValidatorChoice(array('choices' => array($this->getObject()->getFrecuenciaconsultaId()), 'empty_value' => $this->getObject()->getFrecuenciaconsultaId(), 'required' => false)),
      'DESCRIPCION'           => new sfValidatorString(array('max_length' => 250, 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('frecuencia_consulta[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'FrecuenciaConsulta';
  }


}
