<?php

/**
 * ReceptorComunicaciones filter form base class.
 *
 * @package    simad
 * @subpackage filter
 * @author     Your name here
 */
abstract class BaseReceptorComunicacionesFormFilter extends BaseFormFilterPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'MODULO_ID'                 => new sfWidgetFormPropelChoice(array('model' => 'Modulo', 'add_empty' => true)),
      'USUARIO_ID'                => new sfWidgetFormPropelChoice(array('model' => 'Usuario', 'add_empty' => true)),
      'TIPOCOMRECIBIDA_ID'        => new sfWidgetFormPropelChoice(array('model' => 'TipoComRecibida', 'add_empty' => true)),
      'CANTIDAD'                  => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'VARIACION'                 => new sfWidgetFormFilterInput(),
    ));

    $this->setValidators(array(
      'MODULO_ID'                 => new sfValidatorPropelChoice(array('required' => false, 'model' => 'Modulo', 'column' => 'MODULO_ID')),
      'USUARIO_ID'                => new sfValidatorPropelChoice(array('required' => false, 'model' => 'Usuario', 'column' => 'USUARIO_ID')),
      'TIPOCOMRECIBIDA_ID'        => new sfValidatorPropelChoice(array('required' => false, 'model' => 'TipoComRecibida', 'column' => 'TIPOCOMRECIBIDA_ID')),
      'CANTIDAD'                  => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'VARIACION'                 => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
    ));

    $this->widgetSchema->setNameFormat('receptor_comunicaciones_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'ReceptorComunicaciones';
  }

  public function getFields()
  {
    return array(
      'RECEPTORCOMUNICACIONES_ID' => 'Number',
      'MODULO_ID'                 => 'ForeignKey',
      'USUARIO_ID'                => 'ForeignKey',
      'TIPOCOMRECIBIDA_ID'        => 'ForeignKey',
      'CANTIDAD'                  => 'Number',
      'VARIACION'                 => 'Number',
    );
  }
}
