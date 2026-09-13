<?php

/**
 * Procesos form base class.
 *
 * @method Procesos getObject() Returns the current form's model object
 *
 * @package    ##PROJECT_NAME##
 * @subpackage form
 * @author     ##AUTHOR_NAME##
 */
abstract class BaseProcesosForm extends BaseFormPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'PROCESOS_ID'     => new sfWidgetFormInputHidden(),
      'MACROPROCESO_ID' => new sfWidgetFormPropelChoice(array('model' => 'MacroProceso', 'add_empty' => false)),
      'DESCRIPCION'     => new sfWidgetFormInputText(),
      'CODIGO'          => new sfWidgetFormInputText(),
    ));

    $this->setValidators(array(
      'PROCESOS_ID'     => new sfValidatorChoice(array('choices' => array($this->getObject()->getProcesosId()), 'empty_value' => $this->getObject()->getProcesosId(), 'required' => false)),
      'MACROPROCESO_ID' => new sfValidatorPropelChoice(array('model' => 'MacroProceso', 'column' => 'MACROPROCESO_ID')),
      'DESCRIPCION'     => new sfValidatorString(array('max_length' => 250, 'required' => false)),
      'CODIGO'          => new sfValidatorString(array('max_length' => 50, 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('procesos[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'Procesos';
  }


}
