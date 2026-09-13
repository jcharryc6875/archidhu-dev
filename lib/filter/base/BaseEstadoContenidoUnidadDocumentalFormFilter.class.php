<?php

/**
 * EstadoContenidoUnidadDocumental filter form base class.
 *
 * @package    simad
 * @subpackage filter
 * @author     Your name here
 */
abstract class BaseEstadoContenidoUnidadDocumentalFormFilter extends BaseFormFilterPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'DESCRIPCION'                 => new sfWidgetFormFilterInput(),
    ));

    $this->setValidators(array(
      'DESCRIPCION'                 => new sfValidatorPass(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('estado_contenido_unidad_documental_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'EstadoContenidoUnidadDocumental';
  }

  public function getFields()
  {
    return array(
      'ESTADOCONTENIDOUNIDADDOC_ID' => 'Number',
      'DESCRIPCION'                 => 'Text',
    );
  }
}
