<?php

/**
 * EstadoUnidadDocumental form base class.
 *
 * @method EstadoUnidadDocumental getObject() Returns the current form's model object
 *
 * @package    ##PROJECT_NAME##
 * @subpackage form
 * @author     ##AUTHOR_NAME##
 */
abstract class BaseEstadoUnidadDocumentalForm extends BaseFormPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'ESTADOUNIDADDOCUMENTAL_ID' => new sfWidgetFormInputHidden(),
      'DESCRIPCION'               => new sfWidgetFormInputText(),
    ));

    $this->setValidators(array(
      'ESTADOUNIDADDOCUMENTAL_ID' => new sfValidatorChoice(array('choices' => array($this->getObject()->getEstadounidaddocumentalId()), 'empty_value' => $this->getObject()->getEstadounidaddocumentalId(), 'required' => false)),
      'DESCRIPCION'               => new sfValidatorString(array('max_length' => 250, 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('estado_unidad_documental[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'EstadoUnidadDocumental';
  }


}
