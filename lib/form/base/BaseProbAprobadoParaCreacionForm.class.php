<?php

/**
 * ProbAprobadoParaCreacion form base class.
 *
 * @method ProbAprobadoParaCreacion getObject() Returns the current form's model object
 *
 * @package    ##PROJECT_NAME##
 * @subpackage form
 * @author     ##AUTHOR_NAME##
 */
abstract class BaseProbAprobadoParaCreacionForm extends BaseFormPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'PROB_APROBADO_PARA_CREACION_ID' => new sfWidgetFormInputHidden(),
      'DESCRIPCION'                    => new sfWidgetFormInputText(),
    ));

    $this->setValidators(array(
      'PROB_APROBADO_PARA_CREACION_ID' => new sfValidatorChoice(array('choices' => array($this->getObject()->getProbAprobadoParaCreacionId()), 'empty_value' => $this->getObject()->getProbAprobadoParaCreacionId(), 'required' => false)),
      'DESCRIPCION'                    => new sfValidatorString(array('max_length' => 250, 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('prob_aprobado_para_creacion[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'ProbAprobadoParaCreacion';
  }


}
