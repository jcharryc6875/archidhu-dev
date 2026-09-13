<?php

/**
 * EnviadaDirectorio filter form base class.
 *
 * @package    simad
 * @subpackage filter
 * @author     Your name here
 */
abstract class BaseEnviadaDirectorioFormFilter extends BaseFormFilterPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'COMENVIADA_ID'        => new sfWidgetFormPropelChoice(array('model' => 'ComEnviada', 'add_empty' => true)),
      'DIRECTORIOEXTERNO_ID' => new sfWidgetFormPropelChoice(array('model' => 'DirectorioExterno', 'add_empty' => true)),
      'ROLDIRENVIADA_ID'     => new sfWidgetFormPropelChoice(array('model' => 'RolDirenviada', 'add_empty' => true)),
    ));

    $this->setValidators(array(
      'COMENVIADA_ID'        => new sfValidatorPropelChoice(array('required' => false, 'model' => 'ComEnviada', 'column' => 'COMENVIADA_ID')),
      'DIRECTORIOEXTERNO_ID' => new sfValidatorPropelChoice(array('required' => false, 'model' => 'DirectorioExterno', 'column' => 'DIRECTORIOEXTERNO_ID')),
      'ROLDIRENVIADA_ID'     => new sfValidatorPropelChoice(array('required' => false, 'model' => 'RolDirenviada', 'column' => 'ROLDIRENVIADA_ID')),
    ));

    $this->widgetSchema->setNameFormat('enviada_directorio_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'EnviadaDirectorio';
  }

  public function getFields()
  {
    return array(
      'ENVIADADIRECTORIO_ID' => 'Number',
      'COMENVIADA_ID'        => 'ForeignKey',
      'DIRECTORIOEXTERNO_ID' => 'ForeignKey',
      'ROLDIRENVIADA_ID'     => 'ForeignKey',
    );
  }
}
