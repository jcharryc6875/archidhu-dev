<?php

/**
 * DescarteFinalSubserie form base class.
 *
 * @method DescarteFinalSubserie getObject() Returns the current form's model object
 *
 * @package    ##PROJECT_NAME##
 * @subpackage form
 * @author     ##AUTHOR_NAME##
 */
abstract class BaseDescarteFinalSubserieForm extends BaseFormPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'DESCARTEFINALSUBSERIE_ID' => new sfWidgetFormInputHidden(),
      'DESCRIPCION'              => new sfWidgetFormInputText(),
    ));

    $this->setValidators(array(
      'DESCARTEFINALSUBSERIE_ID' => new sfValidatorChoice(array('choices' => array($this->getObject()->getDescartefinalsubserieId()), 'empty_value' => $this->getObject()->getDescartefinalsubserieId(), 'required' => false)),
      'DESCRIPCION'              => new sfValidatorString(array('max_length' => 250, 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('descarte_final_subserie[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'DescarteFinalSubserie';
  }


}
