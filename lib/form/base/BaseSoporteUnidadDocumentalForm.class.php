<?php

/**
 * SoporteUnidadDocumental form base class.
 *
 * @method SoporteUnidadDocumental getObject() Returns the current form's model object
 *
 * @package    ##PROJECT_NAME##
 * @subpackage form
 * @author     ##AUTHOR_NAME##
 */
abstract class BaseSoporteUnidadDocumentalForm extends BaseFormPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'SOPORTEUNIDADDOCUMENTAL_ID' => new sfWidgetFormInputHidden(),
      'DESCRIPCION'                => new sfWidgetFormInputText(),
    ));

    $this->setValidators(array(
      'SOPORTEUNIDADDOCUMENTAL_ID' => new sfValidatorChoice(array('choices' => array($this->getObject()->getSoporteunidaddocumentalId()), 'empty_value' => $this->getObject()->getSoporteunidaddocumentalId(), 'required' => false)),
      'DESCRIPCION'                => new sfValidatorString(array('max_length' => 250, 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('soporte_unidad_documental[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'SoporteUnidadDocumental';
  }


}
