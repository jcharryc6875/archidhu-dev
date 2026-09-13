<?php

/**
 * MensageEmail filter form base class.
 *
 * @package    simad
 * @subpackage filter
 * @author     Your name here
 */
abstract class BaseMensageEmailFormFilter extends BaseFormFilterPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'FORMATO_MENSAJE' => new sfWidgetFormFilterInput(),
    ));

    $this->setValidators(array(
      'FORMATO_MENSAJE' => new sfValidatorPass(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('mensage_email_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'MensageEmail';
  }

  public function getFields()
  {
    return array(
      'MENSAGEEMAIL_ID' => 'Number',
      'FORMATO_MENSAJE' => 'Text',
    );
  }
}
