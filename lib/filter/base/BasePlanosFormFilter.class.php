<?php

/**
 * Planos filter form base class.
 *
 * @package    simad
 * @subpackage filter
 * @author     Your name here
 */
abstract class BasePlanosFormFilter extends BaseFormFilterPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'ESPECIALIDADPLANO_ID' => new sfWidgetFormPropelChoice(array('model' => 'EspecialidadPlano', 'add_empty' => true)),
      'DOCUMENTACION_ID'     => new sfWidgetFormPropelChoice(array('model' => 'Documentacion', 'add_empty' => true)),
      'TIPOESCALA_ID'        => new sfWidgetFormPropelChoice(array('model' => 'TipoEscala', 'add_empty' => true)),
      'CATEGORIAESCALA_ID'   => new sfWidgetFormPropelChoice(array('model' => 'CategoriaEscala', 'add_empty' => true)),
      'VERSION'              => new sfWidgetFormFilterInput(),
      'PAGINACION'           => new sfWidgetFormFilterInput(),
      'ILUSTRACIONES'        => new sfWidgetFormFilterInput(),
      'MENCION_ESCALA'       => new sfWidgetFormFilterInput(),
      'MENCION_PROYECCION'   => new sfWidgetFormFilterInput(),
      'MENCION_COORDENADAS'  => new sfWidgetFormFilterInput(),
      'TAMANO'               => new sfWidgetFormFilterInput(),
    ));

    $this->setValidators(array(
      'ESPECIALIDADPLANO_ID' => new sfValidatorPropelChoice(array('required' => false, 'model' => 'EspecialidadPlano', 'column' => 'ESPECIALIDADPLANO_ID')),
      'DOCUMENTACION_ID'     => new sfValidatorPropelChoice(array('required' => false, 'model' => 'Documentacion', 'column' => 'DOCUMENTACION_ID')),
      'TIPOESCALA_ID'        => new sfValidatorPropelChoice(array('required' => false, 'model' => 'TipoEscala', 'column' => 'TIPOESCALA_ID')),
      'CATEGORIAESCALA_ID'   => new sfValidatorPropelChoice(array('required' => false, 'model' => 'CategoriaEscala', 'column' => 'CATEGORIAESCALA_ID')),
      'VERSION'              => new sfValidatorSchemaFilter('text', new sfValidatorNumber(array('required' => false))),
      'PAGINACION'           => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'ILUSTRACIONES'        => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'MENCION_ESCALA'       => new sfValidatorPass(array('required' => false)),
      'MENCION_PROYECCION'   => new sfValidatorPass(array('required' => false)),
      'MENCION_COORDENADAS'  => new sfValidatorPass(array('required' => false)),
      'TAMANO'               => new sfValidatorSchemaFilter('text', new sfValidatorNumber(array('required' => false))),
    ));

    $this->widgetSchema->setNameFormat('planos_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'Planos';
  }

  public function getFields()
  {
    return array(
      'PLANOS_ID'            => 'Number',
      'ESPECIALIDADPLANO_ID' => 'ForeignKey',
      'DOCUMENTACION_ID'     => 'ForeignKey',
      'TIPOESCALA_ID'        => 'ForeignKey',
      'CATEGORIAESCALA_ID'   => 'ForeignKey',
      'VERSION'              => 'Number',
      'PAGINACION'           => 'Number',
      'ILUSTRACIONES'        => 'Number',
      'MENCION_ESCALA'       => 'Text',
      'MENCION_PROYECCION'   => 'Text',
      'MENCION_COORDENADAS'  => 'Text',
      'TAMANO'               => 'Number',
    );
  }
}
