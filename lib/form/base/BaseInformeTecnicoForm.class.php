<?php

/**
 * InformeTecnico form base class.
 *
 * @method InformeTecnico getObject() Returns the current form's model object
 *
 * @package    ##PROJECT_NAME##
 * @subpackage form
 * @author     ##AUTHOR_NAME##
 */
abstract class BaseInformeTecnicoForm extends BaseFormPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'INFORMETECNICO_ID'       => new sfWidgetFormInputHidden(),
      'DOCUMENTACION_ID'        => new sfWidgetFormPropelChoice(array('model' => 'Documentacion', 'add_empty' => false)),
      'PAGINACION'              => new sfWidgetFormInputText(),
      'ILUSTRACIONES'           => new sfWidgetFormInputText(),
      'VOLUMEN'                 => new sfWidgetFormInputText(),
      'MATERIAL_COMPLEMENTARIO' => new sfWidgetFormInputText(),
      'NOTAS_CONTENIDO'         => new sfWidgetFormInputText(),
    ));

    $this->setValidators(array(
      'INFORMETECNICO_ID'       => new sfValidatorChoice(array('choices' => array($this->getObject()->getInformetecnicoId()), 'empty_value' => $this->getObject()->getInformetecnicoId(), 'required' => false)),
      'DOCUMENTACION_ID'        => new sfValidatorPropelChoice(array('model' => 'Documentacion', 'column' => 'DOCUMENTACION_ID')),
      'PAGINACION'              => new sfValidatorInteger(array('min' => -2147483648, 'max' => 2147483647, 'required' => false)),
      'ILUSTRACIONES'           => new sfValidatorInteger(array('min' => -128, 'max' => 127, 'required' => false)),
      'VOLUMEN'                 => new sfValidatorNumber(array('required' => false)),
      'MATERIAL_COMPLEMENTARIO' => new sfValidatorString(array('max_length' => 30, 'required' => false)),
      'NOTAS_CONTENIDO'         => new sfValidatorString(array('max_length' => 50, 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('informe_tecnico[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'InformeTecnico';
  }


}
