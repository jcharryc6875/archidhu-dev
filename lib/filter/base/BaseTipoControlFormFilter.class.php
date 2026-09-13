<?php

/**
 * TipoControl filter form base class.
 *
 * @package    simad
 * @subpackage filter
 * @author     Your name here
 */
abstract class BaseTipoControlFormFilter extends BaseFormFilterPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'MODULO_ID'      => new sfWidgetFormPropelChoice(array('model' => 'Modulo', 'add_empty' => true)),
      'DESCRIPCION'    => new sfWidgetFormFilterInput(),
    ));

    $this->setValidators(array(
      'MODULO_ID'      => new sfValidatorPropelChoice(array('required' => false, 'model' => 'Modulo', 'column' => 'MODULO_ID')),
      'DESCRIPCION'    => new sfValidatorPass(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('tipo_control_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'TipoControl';
  }

  public function getFields()
  {
    return array(
      'TIPOCONTROL_ID' => 'Number',
      'MODULO_ID'      => 'ForeignKey',
      'DESCRIPCION'    => 'Text',
    );
  }
}
