<?php

/**
 * AsuntoRecibida filter form base class.
 *
 * @package    simad
 * @subpackage filter
 * @author     Your name here
 */
abstract class BaseAsuntoRecibidaFormFilter extends BaseFormFilterPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'DESCRIPCION'        => new sfWidgetFormFilterInput(),
      'VENTANILLA_DEFAULT' => new sfWidgetFormFilterInput(),
    ));

    $this->setValidators(array(
      'DESCRIPCION'        => new sfValidatorPass(array('required' => false)),
      'VENTANILLA_DEFAULT' => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
    ));

    $this->widgetSchema->setNameFormat('asunto_recibida_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'AsuntoRecibida';
  }

  public function getFields()
  {
    return array(
      'ASUNTORECIBIDA_ID'  => 'Number',
      'DESCRIPCION'        => 'Text',
      'VENTANILLA_DEFAULT' => 'Number',
    );
  }
}
