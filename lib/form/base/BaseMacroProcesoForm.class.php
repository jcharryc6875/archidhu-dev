<?php

/**
 * MacroProceso form base class.
 *
 * @method MacroProceso getObject() Returns the current form's model object
 *
 * @package    ##PROJECT_NAME##
 * @subpackage form
 * @author     ##AUTHOR_NAME##
 */
abstract class BaseMacroProcesoForm extends BaseFormPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'MACROPROCESO_ID' => new sfWidgetFormInputHidden(),
      'DESCRIPCION'     => new sfWidgetFormInputText(),
      'CODIGO'          => new sfWidgetFormInputText(),
    ));

    $this->setValidators(array(
      'MACROPROCESO_ID' => new sfValidatorChoice(array('choices' => array($this->getObject()->getMacroprocesoId()), 'empty_value' => $this->getObject()->getMacroprocesoId(), 'required' => false)),
      'DESCRIPCION'     => new sfValidatorString(array('max_length' => 250, 'required' => false)),
      'CODIGO'          => new sfValidatorString(array('max_length' => 50, 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('macro_proceso[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'MacroProceso';
  }


}
