<?php

/**
 * Isad form base class.
 *
 * @method Isad getObject() Returns the current form's model object
 *
 * @package    ##PROJECT_NAME##
 * @subpackage form
 * @author     ##AUTHOR_NAME##
 */
abstract class BaseIsadForm extends BaseFormPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'ISAD_ID'                           => new sfWidgetFormInputHidden(),
      'UNIDADDOCUMENTAL_ID'               => new sfWidgetFormPropelChoice(array('model' => 'UnidadDocumental', 'add_empty' => false)),
      'NIVELDESCRIPCION_ID'               => new sfWidgetFormPropelChoice(array('model' => 'NivelDescripcion', 'add_empty' => false)),
      'CODIGO_REFERENCIA'                 => new sfWidgetFormInputText(),
      'TITULO'                            => new sfWidgetFormInputText(),
      'FECHA_ACUMULACION'                 => new sfWidgetFormDateTime(),
      'NOMBRE_PRODUCTOR'                  => new sfWidgetFormInputText(),
      'RESENA_BIBLIOGRAFICA'              => new sfWidgetFormInputText(),
      'HISTORIA_ARCHIVISTICA'             => new sfWidgetFormInputText(),
      'FORMA_INGRESO'                     => new sfWidgetFormInputText(),
      'ALCANCE_CONTENIDO'                 => new sfWidgetFormInputText(),
      'VALORACION_SELECCION_ELIMINACION'  => new sfWidgetFormInputText(),
      'NUEVOS_INGRESOS'                   => new sfWidgetFormInputText(),
      'ORGANIZACION'                      => new sfWidgetFormInputText(),
      'CONDICIONES_ACCESO'                => new sfWidgetFormInputText(),
      'CONDICIONES_REPRODUCCION'          => new sfWidgetFormInputText(),
      'LENGUA_ESCRITURA_DOCUMETOS'        => new sfWidgetFormInputText(),
      'CARACTERISTICAS_FISICAS'           => new sfWidgetFormInputText(),
      'INTRUMENTOS_DESCRIPCION'           => new sfWidgetFormInputText(),
      'LOCALIZACION_ORIGINALES'           => new sfWidgetFormInputText(),
      'LOCALIZACION_COPIAS'               => new sfWidgetFormInputText(),
      'UNIDADES_DESCRIPCION_RELACIONADAS' => new sfWidgetFormInputText(),
      'NOTA_DESCRIPCION'                  => new sfWidgetFormInputText(),
      'NOTAS'                             => new sfWidgetFormInputText(),
      'NOTA_ARCHIVERO'                    => new sfWidgetFormInputText(),
      'REGLAS_NORMAS'                     => new sfWidgetFormInputText(),
      'FECHA_DESCRIPCIONES'               => new sfWidgetFormDateTime(),
    ));

    $this->setValidators(array(
      'ISAD_ID'                           => new sfValidatorChoice(array('choices' => array($this->getObject()->getIsadId()), 'empty_value' => $this->getObject()->getIsadId(), 'required' => false)),
      'UNIDADDOCUMENTAL_ID'               => new sfValidatorPropelChoice(array('model' => 'UnidadDocumental', 'column' => 'UNIDADDOCUMENTAL_ID')),
      'NIVELDESCRIPCION_ID'               => new sfValidatorPropelChoice(array('model' => 'NivelDescripcion', 'column' => 'NIVELDESCRIPCION_ID')),
      'CODIGO_REFERENCIA'                 => new sfValidatorString(array('max_length' => 50, 'required' => false)),
      'TITULO'                            => new sfValidatorString(array('max_length' => 300, 'required' => false)),
      'FECHA_ACUMULACION'                 => new sfValidatorDateTime(array('required' => false)),
      'NOMBRE_PRODUCTOR'                  => new sfValidatorString(array('max_length' => 50, 'required' => false)),
      'RESENA_BIBLIOGRAFICA'              => new sfValidatorString(array('max_length' => 50, 'required' => false)),
      'HISTORIA_ARCHIVISTICA'             => new sfValidatorString(array('max_length' => 50, 'required' => false)),
      'FORMA_INGRESO'                     => new sfValidatorString(array('max_length' => 50, 'required' => false)),
      'ALCANCE_CONTENIDO'                 => new sfValidatorString(array('max_length' => 50, 'required' => false)),
      'VALORACION_SELECCION_ELIMINACION'  => new sfValidatorString(array('max_length' => 50, 'required' => false)),
      'NUEVOS_INGRESOS'                   => new sfValidatorString(array('max_length' => 50, 'required' => false)),
      'ORGANIZACION'                      => new sfValidatorString(array('max_length' => 50, 'required' => false)),
      'CONDICIONES_ACCESO'                => new sfValidatorString(array('max_length' => 50, 'required' => false)),
      'CONDICIONES_REPRODUCCION'          => new sfValidatorString(array('max_length' => 50, 'required' => false)),
      'LENGUA_ESCRITURA_DOCUMETOS'        => new sfValidatorString(array('max_length' => 50, 'required' => false)),
      'CARACTERISTICAS_FISICAS'           => new sfValidatorString(array('max_length' => 50, 'required' => false)),
      'INTRUMENTOS_DESCRIPCION'           => new sfValidatorString(array('max_length' => 50, 'required' => false)),
      'LOCALIZACION_ORIGINALES'           => new sfValidatorString(array('max_length' => 50, 'required' => false)),
      'LOCALIZACION_COPIAS'               => new sfValidatorString(array('max_length' => 50, 'required' => false)),
      'UNIDADES_DESCRIPCION_RELACIONADAS' => new sfValidatorString(array('max_length' => 50, 'required' => false)),
      'NOTA_DESCRIPCION'                  => new sfValidatorString(array('max_length' => 100, 'required' => false)),
      'NOTAS'                             => new sfValidatorString(array('max_length' => 300, 'required' => false)),
      'NOTA_ARCHIVERO'                    => new sfValidatorString(array('max_length' => 50, 'required' => false)),
      'REGLAS_NORMAS'                     => new sfValidatorString(array('max_length' => 100, 'required' => false)),
      'FECHA_DESCRIPCIONES'               => new sfValidatorDateTime(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('isad[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'Isad';
  }


}
