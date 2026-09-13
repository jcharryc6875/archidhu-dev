<?php

/**
 * RUsuarioDocumentacion filter form base class.
 *
 * @package    simad
 * @subpackage filter
 * @author     Your name here
 */
abstract class BaseRUsuarioDocumentacionFormFilter extends BaseFormFilterPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'DESCRIPCION'              => new sfWidgetFormFilterInput(),
    ));

    $this->setValidators(array(
      'DESCRIPCION'              => new sfValidatorPass(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('r_usuario_documentacion_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'RUsuarioDocumentacion';
  }

  public function getFields()
  {
    return array(
      'RUSUARIODOCUMENTACION_ID' => 'Number',
      'DESCRIPCION'              => 'Text',
    );
  }
}
