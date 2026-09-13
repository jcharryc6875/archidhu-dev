<?php

/**
 * DescartePorSubserie form base class.
 *
 * @method DescartePorSubserie getObject() Returns the current form's model object
 *
 * @package    ##PROJECT_NAME##
 * @subpackage form
 * @author     ##AUTHOR_NAME##
 */
abstract class BaseDescartePorSubserieForm extends BaseFormPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'DESCARTEPORSUBSERIE_ID'   => new sfWidgetFormInputHidden(),
      'SUBSERIE_ID'              => new sfWidgetFormPropelChoice(array('model' => 'Subserie', 'add_empty' => false)),
      'DESCARTEFINALSUBSERIE_ID' => new sfWidgetFormPropelChoice(array('model' => 'DescarteFinalSubserie', 'add_empty' => false)),
    ));

    $this->setValidators(array(
      'DESCARTEPORSUBSERIE_ID'   => new sfValidatorChoice(array('choices' => array($this->getObject()->getDescarteporsubserieId()), 'empty_value' => $this->getObject()->getDescarteporsubserieId(), 'required' => false)),
      'SUBSERIE_ID'              => new sfValidatorPropelChoice(array('model' => 'Subserie', 'column' => 'SUBSERIE_ID')),
      'DESCARTEFINALSUBSERIE_ID' => new sfValidatorPropelChoice(array('model' => 'DescarteFinalSubserie', 'column' => 'DESCARTEFINALSUBSERIE_ID')),
    ));

    $this->widgetSchema->setNameFormat('descarte_por_subserie[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'DescartePorSubserie';
  }


}
