<?php

/**
 * RolusComenviada form base class.
 *
 * @method RolusComenviada getObject() Returns the current form's model object
 *
 * @package    ##PROJECT_NAME##
 * @subpackage form
 * @author     ##AUTHOR_NAME##
 */
abstract class BaseRolusComenviadaForm extends BaseFormPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'ROLUSCOMENVIADA_ID' => new sfWidgetFormInputHidden(),
      'DESCRIPCION'        => new sfWidgetFormInputText(),
    ));

    $this->setValidators(array(
      'ROLUSCOMENVIADA_ID' => new sfValidatorChoice(array('choices' => array($this->getObject()->getRoluscomenviadaId()), 'empty_value' => $this->getObject()->getRoluscomenviadaId(), 'required' => false)),
      'DESCRIPCION'        => new sfValidatorString(array('max_length' => 250, 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('rolus_comenviada[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'RolusComenviada';
  }


}
