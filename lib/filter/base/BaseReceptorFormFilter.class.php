<?php

/**
 * Receptor filter form base class.
 *
 * @package    simad
 * @subpackage filter
 * @author     Your name here
 */
abstract class BaseReceptorFormFilter extends BaseFormFilterPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'MODULO_ID'      => new sfWidgetFormPropelChoice(array('model' => 'Modulo', 'add_empty' => true)),
      'REGIONAL_ID'    => new sfWidgetFormPropelChoice(array('model' => 'Regional', 'add_empty' => true)),
      'USUARIO_ID'     => new sfWidgetFormPropelChoice(array('model' => 'Usuario', 'add_empty' => true)),
      'DEPENDENCIA_ID' => new sfWidgetFormPropelChoice(array('model' => 'Dependencia', 'add_empty' => true)),
      'ESTA_ACTIVO'    => new sfWidgetFormFilterInput(),
    ));

    $this->setValidators(array(
      'MODULO_ID'      => new sfValidatorPropelChoice(array('required' => false, 'model' => 'Modulo', 'column' => 'MODULO_ID')),
      'REGIONAL_ID'    => new sfValidatorPropelChoice(array('required' => false, 'model' => 'Regional', 'column' => 'REGIONAL_ID')),
      'USUARIO_ID'     => new sfValidatorPropelChoice(array('required' => false, 'model' => 'Usuario', 'column' => 'USUARIO_ID')),
      'DEPENDENCIA_ID' => new sfValidatorPropelChoice(array('required' => false, 'model' => 'Dependencia', 'column' => 'DEPENDENCIA_ID')),
      'ESTA_ACTIVO'    => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
    ));

    $this->widgetSchema->setNameFormat('receptor_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'Receptor';
  }

  public function getFields()
  {
    return array(
      'RECEPTOR_ID'    => 'Number',
      'MODULO_ID'      => 'ForeignKey',
      'REGIONAL_ID'    => 'ForeignKey',
      'USUARIO_ID'     => 'ForeignKey',
      'DEPENDENCIA_ID' => 'ForeignKey',
      'ESTA_ACTIVO'    => 'Number',
    );
  }
}
