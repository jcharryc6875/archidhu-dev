<?php

/**
 * Regional form base class.
 *
 * @method Regional getObject() Returns the current form's model object
 *
 * @package    ##PROJECT_NAME##
 * @subpackage form
 * @author     ##AUTHOR_NAME##
 */
abstract class BaseRegionalForm extends BaseFormPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'REGIONAL_ID'     => new sfWidgetFormInputHidden(),
      'CIUDAD_ID'       => new sfWidgetFormPropelChoice(array('model' => 'Ciudad', 'add_empty' => false)),
      'DESCRIPCION'     => new sfWidgetFormInputText(),
      'DIRECCION'       => new sfWidgetFormTextarea(),
      'ENTIDAD_ID'      => new sfWidgetFormPropelChoice(array('model' => 'Entidad', 'add_empty' => false)),
      'DIRECTORIO_NAME' => new sfWidgetFormInputText(),
      'ES_VISIBLE'      => new sfWidgetFormInputText(),
      'IMAGE_MEMBRETE'  => new sfWidgetFormInputText(),
      'CODIGO'          => new sfWidgetFormInputText(),
    ));

    $this->setValidators(array(
      'REGIONAL_ID'     => new sfValidatorChoice(array('choices' => array($this->getObject()->getRegionalId()), 'empty_value' => $this->getObject()->getRegionalId(), 'required' => false)),
      'CIUDAD_ID'       => new sfValidatorPropelChoice(array('model' => 'Ciudad', 'column' => 'CIUDAD_ID')),
      'DESCRIPCION'     => new sfValidatorString(array('max_length' => 250, 'required' => false)),
      'DIRECCION'       => new sfValidatorString(array('required' => false)),
      'ENTIDAD_ID'      => new sfValidatorPropelChoice(array('model' => 'Entidad', 'column' => 'ENTIDAD_ID')),
      'DIRECTORIO_NAME' => new sfValidatorString(array('max_length' => 50, 'required' => false)),
      'ES_VISIBLE'      => new sfValidatorInteger(array('min' => -128, 'max' => 127, 'required' => false)),
      'IMAGE_MEMBRETE'  => new sfValidatorString(array('max_length' => 80, 'required' => false)),
      'CODIGO'          => new sfValidatorString(array('max_length' => 10, 'required' => false)),
    ));

    $this->validatorSchema->setPostValidator(
      new sfValidatorPropelUnique(array('model' => 'Regional', 'column' => array('DIRECTORIO_NAME')))
    );

    $this->widgetSchema->setNameFormat('regional[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'Regional';
  }


}
