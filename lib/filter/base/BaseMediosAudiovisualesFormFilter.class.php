<?php

/**
 * MediosAudiovisuales filter form base class.
 *
 * @package    simad
 * @subpackage filter
 * @author     Your name here
 */
abstract class BaseMediosAudiovisualesFormFilter extends BaseFormFilterPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'FORMATO_ID'              => new sfWidgetFormPropelChoice(array('model' => 'Formato', 'add_empty' => true)),
      'DOCUMENTACION_ID'        => new sfWidgetFormPropelChoice(array('model' => 'Documentacion', 'add_empty' => true)),
      'VOLUMEN'                 => new sfWidgetFormFilterInput(),
      'MATERIAL_COMPLEMENTARIO' => new sfWidgetFormFilterInput(),
      'NOTAS_CONTENIDO'         => new sfWidgetFormFilterInput(),
    ));

    $this->setValidators(array(
      'FORMATO_ID'              => new sfValidatorPropelChoice(array('required' => false, 'model' => 'Formato', 'column' => 'FORMATO_ID')),
      'DOCUMENTACION_ID'        => new sfValidatorPropelChoice(array('required' => false, 'model' => 'Documentacion', 'column' => 'DOCUMENTACION_ID')),
      'VOLUMEN'                 => new sfValidatorSchemaFilter('text', new sfValidatorNumber(array('required' => false))),
      'MATERIAL_COMPLEMENTARIO' => new sfValidatorPass(array('required' => false)),
      'NOTAS_CONTENIDO'         => new sfValidatorPass(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('medios_audiovisuales_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'MediosAudiovisuales';
  }

  public function getFields()
  {
    return array(
      'MEDIOSAUDIOVISUALES_ID'  => 'Number',
      'FORMATO_ID'              => 'ForeignKey',
      'DOCUMENTACION_ID'        => 'ForeignKey',
      'VOLUMEN'                 => 'Number',
      'MATERIAL_COMPLEMENTARIO' => 'Text',
      'NOTAS_CONTENIDO'         => 'Text',
    );
  }
}
