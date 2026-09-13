<?php

/**
 * Forma filter form base class.
 *
 * @package    simad
 * @subpackage filter
 * @author     Your name here
 */
abstract class BaseFormaFormFilter extends BaseFormFilterPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'MODULO_ID'   => new sfWidgetFormPropelChoice(array('model' => 'Modulo', 'add_empty' => true)),
      'NOMBRE'      => new sfWidgetFormFilterInput(),
      'DESCRIPCION' => new sfWidgetFormFilterInput(),
      'RUTA'        => new sfWidgetFormFilterInput(),
      'IS_PUBLIC'   => new sfWidgetFormFilterInput(),
    ));

    $this->setValidators(array(
      'MODULO_ID'   => new sfValidatorPropelChoice(array('required' => false, 'model' => 'Modulo', 'column' => 'MODULO_ID')),
      'NOMBRE'      => new sfValidatorPass(array('required' => false)),
      'DESCRIPCION' => new sfValidatorPass(array('required' => false)),
      'RUTA'        => new sfValidatorPass(array('required' => false)),
      'IS_PUBLIC'   => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
    ));

    $this->widgetSchema->setNameFormat('forma_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'Forma';
  }

  public function getFields()
  {
    return array(
      'FORMA_ID'    => 'Number',
      'MODULO_ID'   => 'ForeignKey',
      'NOMBRE'      => 'Text',
      'DESCRIPCION' => 'Text',
      'RUTA'        => 'Text',
      'IS_PUBLIC'   => 'Number',
    );
  }
}
