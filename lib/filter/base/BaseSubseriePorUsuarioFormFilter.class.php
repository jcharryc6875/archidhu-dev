<?php

/**
 * SubseriePorUsuario filter form base class.
 *
 * @package    simad
 * @subpackage filter
 * @author     Your name here
 */
abstract class BaseSubseriePorUsuarioFormFilter extends BaseFormFilterPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'SUBSERIE_ID'           => new sfWidgetFormPropelChoice(array('model' => 'Subserie', 'add_empty' => true)),
      'USUARIO_ID'            => new sfWidgetFormPropelChoice(array('model' => 'Usuario', 'add_empty' => true)),
      'CREACION'              => new sfWidgetFormFilterInput(),
      'PRESTAMO'              => new sfWidgetFormFilterInput(),
      'VISUALIZACION'         => new sfWidgetFormFilterInput(),
    ));

    $this->setValidators(array(
      'SUBSERIE_ID'           => new sfValidatorPropelChoice(array('required' => false, 'model' => 'Subserie', 'column' => 'SUBSERIE_ID')),
      'USUARIO_ID'            => new sfValidatorPropelChoice(array('required' => false, 'model' => 'Usuario', 'column' => 'USUARIO_ID')),
      'CREACION'              => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'PRESTAMO'              => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'VISUALIZACION'         => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
    ));

    $this->widgetSchema->setNameFormat('subserie_por_usuario_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'SubseriePorUsuario';
  }

  public function getFields()
  {
    return array(
      'SUBSERIEPORUSUARIO_ID' => 'Number',
      'SUBSERIE_ID'           => 'ForeignKey',
      'USUARIO_ID'            => 'ForeignKey',
      'CREACION'              => 'Number',
      'PRESTAMO'              => 'Number',
      'VISUALIZACION'         => 'Number',
    );
  }
}
