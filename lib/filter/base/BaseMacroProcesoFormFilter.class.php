<?php

/**
 * MacroProceso filter form base class.
 *
 * @package    simad
 * @subpackage filter
 * @author     Your name here
 */
abstract class BaseMacroProcesoFormFilter extends BaseFormFilterPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'DESCRIPCION'     => new sfWidgetFormFilterInput(),
      'CODIGO'          => new sfWidgetFormFilterInput(),
    ));

    $this->setValidators(array(
      'DESCRIPCION'     => new sfValidatorPass(array('required' => false)),
      'CODIGO'          => new sfValidatorPass(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('macro_proceso_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'MacroProceso';
  }

  public function getFields()
  {
    return array(
      'MACROPROCESO_ID' => 'Number',
      'DESCRIPCION'     => 'Text',
      'CODIGO'          => 'Text',
    );
  }
}
