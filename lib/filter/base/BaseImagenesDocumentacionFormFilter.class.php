<?php

/**
 * ImagenesDocumentacion filter form base class.
 *
 * @package    simad
 * @subpackage filter
 * @author     Your name here
 */
abstract class BaseImagenesDocumentacionFormFilter extends BaseFormFilterPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'DOCUMENTACION_ID'         => new sfWidgetFormPropelChoice(array('model' => 'Documentacion', 'add_empty' => true)),
      'DESCRIPCION'              => new sfWidgetFormFilterInput(),
      'RUTA'                     => new sfWidgetFormFilterInput(),
      'FOLIOS'                   => new sfWidgetFormFilterInput(),
      'VINCULADA'                => new sfWidgetFormFilterInput(),
    ));

    $this->setValidators(array(
      'DOCUMENTACION_ID'         => new sfValidatorPropelChoice(array('required' => false, 'model' => 'Documentacion', 'column' => 'DOCUMENTACION_ID')),
      'DESCRIPCION'              => new sfValidatorPass(array('required' => false)),
      'RUTA'                     => new sfValidatorPass(array('required' => false)),
      'FOLIOS'                   => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'VINCULADA'                => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
    ));

    $this->widgetSchema->setNameFormat('imagenes_documentacion_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'ImagenesDocumentacion';
  }

  public function getFields()
  {
    return array(
      'IMAGENESDOCUMENTACION_ID' => 'Number',
      'DOCUMENTACION_ID'         => 'ForeignKey',
      'DESCRIPCION'              => 'Text',
      'RUTA'                     => 'Text',
      'FOLIOS'                   => 'Number',
      'VINCULADA'                => 'Number',
    );
  }
}
