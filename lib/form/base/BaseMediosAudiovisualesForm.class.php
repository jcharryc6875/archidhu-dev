<?php

/**
 * MediosAudiovisuales form base class.
 *
 * @method MediosAudiovisuales getObject() Returns the current form's model object
 *
 * @package    ##PROJECT_NAME##
 * @subpackage form
 * @author     ##AUTHOR_NAME##
 */
abstract class BaseMediosAudiovisualesForm extends BaseFormPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'MEDIOSAUDIOVISUALES_ID'  => new sfWidgetFormInputHidden(),
      'FORMATO_ID'              => new sfWidgetFormPropelChoice(array('model' => 'Formato', 'add_empty' => false)),
      'DOCUMENTACION_ID'        => new sfWidgetFormPropelChoice(array('model' => 'Documentacion', 'add_empty' => false)),
      'VOLUMEN'                 => new sfWidgetFormInputText(),
      'MATERIAL_COMPLEMENTARIO' => new sfWidgetFormInputText(),
      'NOTAS_CONTENIDO'         => new sfWidgetFormInputText(),
    ));

    $this->setValidators(array(
      'MEDIOSAUDIOVISUALES_ID'  => new sfValidatorChoice(array('choices' => array($this->getObject()->getMediosaudiovisualesId()), 'empty_value' => $this->getObject()->getMediosaudiovisualesId(), 'required' => false)),
      'FORMATO_ID'              => new sfValidatorPropelChoice(array('model' => 'Formato', 'column' => 'FORMATO_ID')),
      'DOCUMENTACION_ID'        => new sfValidatorPropelChoice(array('model' => 'Documentacion', 'column' => 'DOCUMENTACION_ID')),
      'VOLUMEN'                 => new sfValidatorNumber(array('required' => false)),
      'MATERIAL_COMPLEMENTARIO' => new sfValidatorString(array('max_length' => 30, 'required' => false)),
      'NOTAS_CONTENIDO'         => new sfValidatorString(array('max_length' => 50, 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('medios_audiovisuales[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'MediosAudiovisuales';
  }


}
