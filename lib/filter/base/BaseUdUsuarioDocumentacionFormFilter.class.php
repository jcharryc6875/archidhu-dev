<?php

/**
 * UdUsuarioDocumentacion filter form base class.
 *
 * @package    simad
 * @subpackage filter
 * @author     Your name here
 */
abstract class BaseUdUsuarioDocumentacionFormFilter extends BaseFormFilterPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'DOCUMENTACION_ID'          => new sfWidgetFormPropelChoice(array('model' => 'Documentacion', 'add_empty' => true)),
      'RUSUARIODOCUMENTACION_ID'  => new sfWidgetFormPropelChoice(array('model' => 'RUsuarioDocumentacion', 'add_empty' => true)),
      'USUARIO_ID'                => new sfWidgetFormPropelChoice(array('model' => 'Usuario', 'add_empty' => true)),
    ));

    $this->setValidators(array(
      'DOCUMENTACION_ID'          => new sfValidatorPropelChoice(array('required' => false, 'model' => 'Documentacion', 'column' => 'DOCUMENTACION_ID')),
      'RUSUARIODOCUMENTACION_ID'  => new sfValidatorPropelChoice(array('required' => false, 'model' => 'RUsuarioDocumentacion', 'column' => 'RUSUARIODOCUMENTACION_ID')),
      'USUARIO_ID'                => new sfValidatorPropelChoice(array('required' => false, 'model' => 'Usuario', 'column' => 'USUARIO_ID')),
    ));

    $this->widgetSchema->setNameFormat('ud_usuario_documentacion_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'UdUsuarioDocumentacion';
  }

  public function getFields()
  {
    return array(
      'UDUSUARIODOCUMENTACION_ID' => 'Number',
      'DOCUMENTACION_ID'          => 'ForeignKey',
      'RUSUARIODOCUMENTACION_ID'  => 'ForeignKey',
      'USUARIO_ID'                => 'ForeignKey',
    );
  }
}
