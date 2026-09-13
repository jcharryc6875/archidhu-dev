<?php

/**
 * FormaRecepcion filter form base class.
 *
 * @package    simad
 * @subpackage filter
 * @author     Your name here
 */
abstract class BaseFormaRecepcionFormFilter extends BaseFormFilterPropel
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

    $this->widgetSchema->setNameFormat('forma_recepcion_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'FormaRecepcion';
  }

  public function getFields()
  {
    return array(
      'FORMARECEPCION_ID'  => 'Number',
      'DESCRIPCION'        => 'Text',
      'VENTANILLA_DEFAULT' => 'Number',
    );
  }
}
