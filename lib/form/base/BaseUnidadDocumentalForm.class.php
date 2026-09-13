<?php

/**
 * UnidadDocumental form base class.
 *
 * @method UnidadDocumental getObject() Returns the current form's model object
 *
 * @package    ##PROJECT_NAME##
 * @subpackage form
 * @author     ##AUTHOR_NAME##
 */
abstract class BaseUnidadDocumentalForm extends BaseFormPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'UNIDADDOCUMENTAL_ID'             => new sfWidgetFormInputHidden(),
      'LOCALIZACIONUNIDADDOCUMENTAL_ID' => new sfWidgetFormPropelChoice(array('model' => 'LocalizacionUnidadDocumental', 'add_empty' => false)),
      'REGIONAL_ID'                     => new sfWidgetFormPropelChoice(array('model' => 'Regional', 'add_empty' => false)),
      'SOPORTEUNIDADDOCUMENTAL_ID'      => new sfWidgetFormPropelChoice(array('model' => 'SoporteUnidadDocumental', 'add_empty' => true)),
      'ESTADOUNIDADDOCUMENTAL_ID'       => new sfWidgetFormPropelChoice(array('model' => 'EstadoUnidadDocumental', 'add_empty' => false)),
      'FRECUENCIACONSULTA_ID'           => new sfWidgetFormPropelChoice(array('model' => 'FrecuenciaConsulta', 'add_empty' => false)),
      'UNIDADCONSERVADORA_ID'           => new sfWidgetFormPropelChoice(array('model' => 'UnidadConservadora', 'add_empty' => false)),
      'SUBSERIE_ID'                     => new sfWidgetFormPropelChoice(array('model' => 'Subserie', 'add_empty' => false)),
      'CODIGO_BARRAS'                   => new sfWidgetFormInputText(),
      'FECHA_APERTURA'                  => new sfWidgetFormDateTime(),
      'FECHA_CIERRE'                    => new sfWidgetFormDateTime(),
      'TITULO'                          => new sfWidgetFormInputText(),
      'CONTENIDO'                       => new sfWidgetFormTextarea(),
      'CREADO_POR_WEB'                  => new sfWidgetFormInputText(),
      'UBICACIONENGESTION'              => new sfWidgetFormInputText(),
      'UBICACIONENCENTRAL'              => new sfWidgetFormInputText(),
      'UBICACIONENHISTORICO'            => new sfWidgetFormInputText(),
      'FOLIOS'                          => new sfWidgetFormInputText(),
      'FECHA_CREACION'                  => new sfWidgetFormDateTime(),
      'NOTAS'                           => new sfWidgetFormInputText(),
      'FECHAVENCIMIENTO'                => new sfWidgetFormDateTime(),
      'VOLUMEN'                         => new sfWidgetFormInputText(),
      'ESTADOTRANSFERENCIA'             => new sfWidgetFormInputText(),
      'MARCA'                           => new sfWidgetFormInputText(),
      'MARCA_ELIMINACION'               => new sfWidgetFormInputText(),
      'OBS_ELIMINACION'                 => new sfWidgetFormInputText(),
      'NUMERO_CAJA'                     => new sfWidgetFormInputText(),
      'NUMERO_IDENTIFICACION'           => new sfWidgetFormInputText(),
    ));

    $this->setValidators(array(
      'UNIDADDOCUMENTAL_ID'             => new sfValidatorChoice(array('choices' => array($this->getObject()->getUnidaddocumentalId()), 'empty_value' => $this->getObject()->getUnidaddocumentalId(), 'required' => false)),
      'LOCALIZACIONUNIDADDOCUMENTAL_ID' => new sfValidatorPropelChoice(array('model' => 'LocalizacionUnidadDocumental', 'column' => 'LOCALIZACIONUNIDADDOCUMENTAL_ID')),
      'REGIONAL_ID'                     => new sfValidatorPropelChoice(array('model' => 'Regional', 'column' => 'REGIONAL_ID')),
      'SOPORTEUNIDADDOCUMENTAL_ID'      => new sfValidatorPropelChoice(array('model' => 'SoporteUnidadDocumental', 'column' => 'SOPORTEUNIDADDOCUMENTAL_ID', 'required' => false)),
      'ESTADOUNIDADDOCUMENTAL_ID'       => new sfValidatorPropelChoice(array('model' => 'EstadoUnidadDocumental', 'column' => 'ESTADOUNIDADDOCUMENTAL_ID')),
      'FRECUENCIACONSULTA_ID'           => new sfValidatorPropelChoice(array('model' => 'FrecuenciaConsulta', 'column' => 'FRECUENCIACONSULTA_ID')),
      'UNIDADCONSERVADORA_ID'           => new sfValidatorPropelChoice(array('model' => 'UnidadConservadora', 'column' => 'UNIDADCONSERVADORA_ID')),
      'SUBSERIE_ID'                     => new sfValidatorPropelChoice(array('model' => 'Subserie', 'column' => 'SUBSERIE_ID')),
      'CODIGO_BARRAS'                   => new sfValidatorString(array('max_length' => 50, 'required' => false)),
      'FECHA_APERTURA'                  => new sfValidatorDateTime(array('required' => false)),
      'FECHA_CIERRE'                    => new sfValidatorDateTime(array('required' => false)),
      'TITULO'                          => new sfValidatorString(array('max_length' => 900, 'required' => false)),
      'CONTENIDO'                       => new sfValidatorString(array('required' => false)),
      'CREADO_POR_WEB'                  => new sfValidatorInteger(array('min' => -128, 'max' => 127, 'required' => false)),
      'UBICACIONENGESTION'              => new sfValidatorString(array('max_length' => 200, 'required' => false)),
      'UBICACIONENCENTRAL'              => new sfValidatorString(array('max_length' => 200, 'required' => false)),
      'UBICACIONENHISTORICO'            => new sfValidatorString(array('max_length' => 200, 'required' => false)),
      'FOLIOS'                          => new sfValidatorInteger(array('min' => -2147483648, 'max' => 2147483647, 'required' => false)),
      'FECHA_CREACION'                  => new sfValidatorDateTime(array('required' => false)),
      'NOTAS'                           => new sfValidatorString(array('max_length' => 4000, 'required' => false)),
      'FECHAVENCIMIENTO'                => new sfValidatorDateTime(array('required' => false)),
      'VOLUMEN'                         => new sfValidatorNumber(array('required' => false)),
      'ESTADOTRANSFERENCIA'             => new sfValidatorInteger(array('min' => -128, 'max' => 127, 'required' => false)),
      'MARCA'                           => new sfValidatorInteger(array('min' => -2147483648, 'max' => 2147483647, 'required' => false)),
      'MARCA_ELIMINACION'               => new sfValidatorString(array('max_length' => 10, 'required' => false)),
      'OBS_ELIMINACION'                 => new sfValidatorString(array('max_length' => 500, 'required' => false)),
      'NUMERO_CAJA'                     => new sfValidatorString(array('max_length' => 80, 'required' => false)),
      'NUMERO_IDENTIFICACION'           => new sfValidatorString(array('max_length' => 100, 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('unidad_documental[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'UnidadDocumental';
  }


}
