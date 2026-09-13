<?php

/**
 * Email filter form base class.
 *
 * @package    simad
 * @subpackage filter
 * @author     Your name here
 */
abstract class BaseEmailFormFilter extends BaseFormFilterPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'EMAIL_ORIGEN'   => new sfWidgetFormFilterInput(),
      'EMAIL_DESTINO'  => new sfWidgetFormFilterInput(),
      'NOMBE_ORIGEN'   => new sfWidgetFormFilterInput(),
      'NOMBRE_DESTINO' => new sfWidgetFormFilterInput(),
      'ASUNTO'         => new sfWidgetFormFilterInput(),
      'CONTENIDO'      => new sfWidgetFormFilterInput(),
      'RUTA'           => new sfWidgetFormFilterInput(),
      'CC'             => new sfWidgetFormFilterInput(),
      'MAIL_ORIGINAL'  => new sfWidgetFormFilterInput(),
    ));

    $this->setValidators(array(
      'EMAIL_ORIGEN'   => new sfValidatorPass(array('required' => false)),
      'EMAIL_DESTINO'  => new sfValidatorPass(array('required' => false)),
      'NOMBE_ORIGEN'   => new sfValidatorPass(array('required' => false)),
      'NOMBRE_DESTINO' => new sfValidatorPass(array('required' => false)),
      'ASUNTO'         => new sfValidatorPass(array('required' => false)),
      'CONTENIDO'      => new sfValidatorPass(array('required' => false)),
      'RUTA'           => new sfValidatorPass(array('required' => false)),
      'CC'             => new sfValidatorPass(array('required' => false)),
      'MAIL_ORIGINAL'  => new sfValidatorPass(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('email_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'Email';
  }

  public function getFields()
  {
    return array(
      'EMAIL_ID'       => 'Number',
      'EMAIL_ORIGEN'   => 'Text',
      'EMAIL_DESTINO'  => 'Text',
      'NOMBE_ORIGEN'   => 'Text',
      'NOMBRE_DESTINO' => 'Text',
      'ASUNTO'         => 'Text',
      'CONTENIDO'      => 'Text',
      'RUTA'           => 'Text',
      'CC'             => 'Text',
      'MAIL_ORIGINAL'  => 'Text',
    );
  }
}
