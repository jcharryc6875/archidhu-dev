<?php

/**
 * MarcaUnidadDocumental form base class.
 *
 * @method MarcaUnidadDocumental getObject() Returns the current form's model object
 *
 * @package    ##PROJECT_NAME##
 * @subpackage form
 * @author     ##AUTHOR_NAME##
 */
abstract class BaseMarcaUnidadDocumentalForm extends BaseFormPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'MARCAUNIDADDOCUMENTAL_ID' => new sfWidgetFormInputHidden(),
      'USUARIO_ID'               => new sfWidgetFormPropelChoice(array('model' => 'Usuario', 'add_empty' => false)),
      'UNIDADDOCUMENTAL_ID'      => new sfWidgetFormPropelChoice(array('model' => 'UnidadDocumental', 'add_empty' => false)),
    ));

    $this->setValidators(array(
      'MARCAUNIDADDOCUMENTAL_ID' => new sfValidatorChoice(array('choices' => array($this->getObject()->getMarcaunidaddocumentalId()), 'empty_value' => $this->getObject()->getMarcaunidaddocumentalId(), 'required' => false)),
      'USUARIO_ID'               => new sfValidatorPropelChoice(array('model' => 'Usuario', 'column' => 'USUARIO_ID')),
      'UNIDADDOCUMENTAL_ID'      => new sfValidatorPropelChoice(array('model' => 'UnidadDocumental', 'column' => 'UNIDADDOCUMENTAL_ID')),
    ));

    $this->widgetSchema->setNameFormat('marca_unidad_documental[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'MarcaUnidadDocumental';
  }


}
