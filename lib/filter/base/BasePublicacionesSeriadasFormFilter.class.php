<?php

/**
 * PublicacionesSeriadas filter form base class.
 *
 * @package    simad
 * @subpackage filter
 * @author     Your name here
 */
abstract class BasePublicacionesSeriadasFormFilter extends BaseFormFilterPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'DOCUMENTACION_ID'         => new sfWidgetFormPropelChoice(array('model' => 'Documentacion', 'add_empty' => true)),
      'MENCION_EDICION'          => new sfWidgetFormFilterInput(),
      'VERSION'                  => new sfWidgetFormFilterInput(),
      'PAGINACION'               => new sfWidgetFormFilterInput(),
      'ILUSTRACIONES'            => new sfWidgetFormFilterInput(),
      'VOLUMEN'                  => new sfWidgetFormFilterInput(),
      'MATERIAL_COMPLEMENTARIO'  => new sfWidgetFormFilterInput(),
      'ISSN'                     => new sfWidgetFormFilterInput(),
      'NOTAS_CONTENIDO'          => new sfWidgetFormFilterInput(),
    ));

    $this->setValidators(array(
      'DOCUMENTACION_ID'         => new sfValidatorPropelChoice(array('required' => false, 'model' => 'Documentacion', 'column' => 'DOCUMENTACION_ID')),
      'MENCION_EDICION'          => new sfValidatorPass(array('required' => false)),
      'VERSION'                  => new sfValidatorSchemaFilter('text', new sfValidatorNumber(array('required' => false))),
      'PAGINACION'               => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'ILUSTRACIONES'            => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'VOLUMEN'                  => new sfValidatorSchemaFilter('text', new sfValidatorNumber(array('required' => false))),
      'MATERIAL_COMPLEMENTARIO'  => new sfValidatorPass(array('required' => false)),
      'ISSN'                     => new sfValidatorPass(array('required' => false)),
      'NOTAS_CONTENIDO'          => new sfValidatorPass(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('publicaciones_seriadas_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'PublicacionesSeriadas';
  }

  public function getFields()
  {
    return array(
      'PUBLICACIONESSERIADAS_ID' => 'Number',
      'DOCUMENTACION_ID'         => 'ForeignKey',
      'MENCION_EDICION'          => 'Text',
      'VERSION'                  => 'Number',
      'PAGINACION'               => 'Number',
      'ILUSTRACIONES'            => 'Number',
      'VOLUMEN'                  => 'Number',
      'MATERIAL_COMPLEMENTARIO'  => 'Text',
      'ISSN'                     => 'Text',
      'NOTAS_CONTENIDO'          => 'Text',
    );
  }
}
