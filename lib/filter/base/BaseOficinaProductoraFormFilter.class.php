<?php

/**
 * OficinaProductora filter form base class.
 *
 * @package    simad
 * @subpackage filter
 * @author     Your name here
 */
abstract class BaseOficinaProductoraFormFilter extends BaseFormFilterPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'PROCESOS_ID'          => new sfWidgetFormPropelChoice(array('model' => 'Procesos', 'add_empty' => true)),
      'DESCRIPCION'          => new sfWidgetFormFilterInput(),
      'CODIGO'               => new sfWidgetFormFilterInput(),
    ));

    $this->setValidators(array(
      'PROCESOS_ID'          => new sfValidatorPropelChoice(array('required' => false, 'model' => 'Procesos', 'column' => 'PROCESOS_ID')),
      'DESCRIPCION'          => new sfValidatorPass(array('required' => false)),
      'CODIGO'               => new sfValidatorPass(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('oficina_productora_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'OficinaProductora';
  }

  public function getFields()
  {
    return array(
      'OFICINAPRODUCTORA_ID' => 'Number',
      'PROCESOS_ID'          => 'ForeignKey',
      'DESCRIPCION'          => 'Text',
      'CODIGO'               => 'Text',
    );
  }
}
