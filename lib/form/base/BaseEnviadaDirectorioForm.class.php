<?php

/**
 * EnviadaDirectorio form base class.
 *
 * @method EnviadaDirectorio getObject() Returns the current form's model object
 *
 * @package    ##PROJECT_NAME##
 * @subpackage form
 * @author     ##AUTHOR_NAME##
 */
abstract class BaseEnviadaDirectorioForm extends BaseFormPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'ENVIADADIRECTORIO_ID' => new sfWidgetFormInputHidden(),
      'COMENVIADA_ID'        => new sfWidgetFormPropelChoice(array('model' => 'ComEnviada', 'add_empty' => false)),
      'DIRECTORIOEXTERNO_ID' => new sfWidgetFormPropelChoice(array('model' => 'DirectorioExterno', 'add_empty' => false)),
      'ROLDIRENVIADA_ID'     => new sfWidgetFormPropelChoice(array('model' => 'RolDirenviada', 'add_empty' => false)),
    ));

    $this->setValidators(array(
      'ENVIADADIRECTORIO_ID' => new sfValidatorChoice(array('choices' => array($this->getObject()->getEnviadadirectorioId()), 'empty_value' => $this->getObject()->getEnviadadirectorioId(), 'required' => false)),
      'COMENVIADA_ID'        => new sfValidatorPropelChoice(array('model' => 'ComEnviada', 'column' => 'COMENVIADA_ID')),
      'DIRECTORIOEXTERNO_ID' => new sfValidatorPropelChoice(array('model' => 'DirectorioExterno', 'column' => 'DIRECTORIOEXTERNO_ID')),
      'ROLDIRENVIADA_ID'     => new sfValidatorPropelChoice(array('model' => 'RolDirenviada', 'column' => 'ROLDIRENVIADA_ID')),
    ));

    $this->widgetSchema->setNameFormat('enviada_directorio[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'EnviadaDirectorio';
  }


}
