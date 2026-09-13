<?php

/**
 * Parametro filter form base class.
 *
 * @package    simad
 * @subpackage filter
 * @author     Your name here
 */
abstract class BaseParametroFormFilter extends BaseFormFilterPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'CODIGO'         => new sfWidgetFormFilterInput(),
      'DESCRIPCION'    => new sfWidgetFormFilterInput(),
      'VALORTEXTO'     => new sfWidgetFormFilterInput(),
      'VALOR_NUMERICO' => new sfWidgetFormFilterInput(),
    ));

    $this->setValidators(array(
      'CODIGO'         => new sfValidatorPass(array('required' => false)),
      'DESCRIPCION'    => new sfValidatorPass(array('required' => false)),
      'VALORTEXTO'     => new sfValidatorPass(array('required' => false)),
      'VALOR_NUMERICO' => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
    ));

    $this->widgetSchema->setNameFormat('parametro_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'Parametro';
  }

  public function getFields()
  {
    return array(
      'PARAMETRO_ID'   => 'Number',
      'CODIGO'         => 'Text',
      'DESCRIPCION'    => 'Text',
      'VALORTEXTO'     => 'Text',
      'VALOR_NUMERICO' => 'Number',
    );
  }
}
