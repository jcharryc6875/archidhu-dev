<?php

/**
 * EmpresaMensajeria filter form base class.
 *
 * @package    simad
 * @subpackage filter
 * @author     Your name here
 */
abstract class BaseEmpresaMensajeriaFormFilter extends BaseFormFilterPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'NOMBRE'                => new sfWidgetFormFilterInput(),
      'URL'                   => new sfWidgetFormFilterInput(),
      'ABREVIATURA'           => new sfWidgetFormFilterInput(),
    ));

    $this->setValidators(array(
      'NOMBRE'                => new sfValidatorPass(array('required' => false)),
      'URL'                   => new sfValidatorPass(array('required' => false)),
      'ABREVIATURA'           => new sfValidatorPass(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('empresa_mensajeria_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'EmpresaMensajeria';
  }

  public function getFields()
  {
    return array(
      'EMPRESA_MENSAJERIA_ID' => 'Number',
      'NOMBRE'                => 'Text',
      'URL'                   => 'Text',
      'ABREVIATURA'           => 'Text',
    );
  }
}
