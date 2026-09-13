<?php

/**
 * LocalizacionUnidadDocumental form base class.
 *
 * @method LocalizacionUnidadDocumental getObject() Returns the current form's model object
 *
 * @package    ##PROJECT_NAME##
 * @subpackage form
 * @author     ##AUTHOR_NAME##
 */
abstract class BaseLocalizacionUnidadDocumentalForm extends BaseFormPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'LOCALIZACIONUNIDADDOCUMENTAL_ID' => new sfWidgetFormInputHidden(),
      'DESCRIPCION'                     => new sfWidgetFormInputText(),
    ));

    $this->setValidators(array(
      'LOCALIZACIONUNIDADDOCUMENTAL_ID' => new sfValidatorChoice(array('choices' => array($this->getObject()->getLocalizacionunidaddocumentalId()), 'empty_value' => $this->getObject()->getLocalizacionunidaddocumentalId(), 'required' => false)),
      'DESCRIPCION'                     => new sfValidatorString(array('max_length' => 250, 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('localizacion_unidad_documental[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'LocalizacionUnidadDocumental';
  }


}
