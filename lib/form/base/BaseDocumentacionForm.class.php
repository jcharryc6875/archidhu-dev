<?php

/**
 * Documentacion form base class.
 *
 * @method Documentacion getObject() Returns the current form's model object
 *
 * @package    ##PROJECT_NAME##
 * @subpackage form
 * @author     ##AUTHOR_NAME##
 */
abstract class BaseDocumentacionForm extends BaseFormPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'DOCUMENTACION_ID'                => new sfWidgetFormInputHidden(),
      'ESTADODOCUMENTACION_ID'          => new sfWidgetFormPropelChoice(array('model' => 'EstadoDocumentacion', 'add_empty' => false)),
      'IDIOMA_ID'                       => new sfWidgetFormPropelChoice(array('model' => 'Idioma', 'add_empty' => false)),
      'TIPODOCUMENTACION_ID'            => new sfWidgetFormPropelChoice(array('model' => 'TipoDocumentacion', 'add_empty' => false)),
      'TITULO'                          => new sfWidgetFormInputText(),
      'OTRA_INFORMACION_TITULO'         => new sfWidgetFormInputText(),
      'PRIMERA_MENCION_RESPONSABILIDAD' => new sfWidgetFormInputText(),
      'MENCION_RESPONSABILIDAD'         => new sfWidgetFormInputText(),
      'NUMERO_CLASIFICACION'            => new sfWidgetFormInputText(),
      'DESCRIPTORES_TEMATICOS'          => new sfWidgetFormInputText(),
      'CODIGO_BARRAS'                   => new sfWidgetFormInputText(),
      'UBICACION'                       => new sfWidgetFormInputText(),
      'RUTA'                            => new sfWidgetFormInputText(),
      'FECHA_CREACION'                  => new sfWidgetFormDateTime(),
      'FECHA_MODIFICACION'              => new sfWidgetFormDateTime(),
    ));

    $this->setValidators(array(
      'DOCUMENTACION_ID'                => new sfValidatorChoice(array('choices' => array($this->getObject()->getDocumentacionId()), 'empty_value' => $this->getObject()->getDocumentacionId(), 'required' => false)),
      'ESTADODOCUMENTACION_ID'          => new sfValidatorPropelChoice(array('model' => 'EstadoDocumentacion', 'column' => 'ESTADODOCUMENTACION_ID')),
      'IDIOMA_ID'                       => new sfValidatorPropelChoice(array('model' => 'Idioma', 'column' => 'IDIOMA_ID')),
      'TIPODOCUMENTACION_ID'            => new sfValidatorPropelChoice(array('model' => 'TipoDocumentacion', 'column' => 'TIPODOCUMENTACION_ID')),
      'TITULO'                          => new sfValidatorString(array('max_length' => 300, 'required' => false)),
      'OTRA_INFORMACION_TITULO'         => new sfValidatorString(array('max_length' => 250, 'required' => false)),
      'PRIMERA_MENCION_RESPONSABILIDAD' => new sfValidatorString(array('max_length' => 250, 'required' => false)),
      'MENCION_RESPONSABILIDAD'         => new sfValidatorString(array('max_length' => 250, 'required' => false)),
      'NUMERO_CLASIFICACION'            => new sfValidatorInteger(array('min' => -2147483648, 'max' => 2147483647, 'required' => false)),
      'DESCRIPTORES_TEMATICOS'          => new sfValidatorString(array('max_length' => 250, 'required' => false)),
      'CODIGO_BARRAS'                   => new sfValidatorString(array('max_length' => 20, 'required' => false)),
      'UBICACION'                       => new sfValidatorString(array('max_length' => 30, 'required' => false)),
      'RUTA'                            => new sfValidatorString(array('max_length' => 1000, 'required' => false)),
      'FECHA_CREACION'                  => new sfValidatorDateTime(array('required' => false)),
      'FECHA_MODIFICACION'              => new sfValidatorDateTime(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('documentacion[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'Documentacion';
  }


}
