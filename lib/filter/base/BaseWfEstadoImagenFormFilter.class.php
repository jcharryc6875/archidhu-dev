<?php

/**
 * WfEstadoImagen filter form base class.
 *
 * @package    simad
 * @subpackage filter
 * @author     Your name here
 */
abstract class BaseWfEstadoImagenFormFilter extends BaseFormFilterPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'nombre'              => new sfWidgetFormFilterInput(array('with_empty' => false)),
    ));

    $this->setValidators(array(
      'nombre'              => new sfValidatorPass(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('wf_estado_imagen_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'WfEstadoImagen';
  }

  public function getFields()
  {
    return array(
      'wf_estado_imagen_id' => 'Number',
      'nombre'              => 'Text',
    );
  }
}
