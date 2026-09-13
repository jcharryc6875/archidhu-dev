<?php

/**
 * RolDirenviada form base class.
 *
 * @method RolDirenviada getObject() Returns the current form's model object
 *
 * @package    ##PROJECT_NAME##
 * @subpackage form
 * @author     ##AUTHOR_NAME##
 */
abstract class BaseRolDirenviadaForm extends BaseFormPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'ROLDIRENVIADA_ID' => new sfWidgetFormInputHidden(),
      'DESCRIPCION'      => new sfWidgetFormInputText(),
    ));

    $this->setValidators(array(
      'ROLDIRENVIADA_ID' => new sfValidatorChoice(array('choices' => array($this->getObject()->getRoldirenviadaId()), 'empty_value' => $this->getObject()->getRoldirenviadaId(), 'required' => false)),
      'DESCRIPCION'      => new sfValidatorString(array('max_length' => 250, 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('rol_direnviada[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'RolDirenviada';
  }


}
