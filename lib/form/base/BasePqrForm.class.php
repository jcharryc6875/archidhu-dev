<?php

/**
 * Pqr form base class.
 *
 * @method Pqr getObject() Returns the current form's model object
 *
 * @package    ##PROJECT_NAME##
 * @subpackage form
 * @author     ##AUTHOR_NAME##
 */
abstract class BasePqrForm extends BaseFormPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'PQR_ID'          => new sfWidgetFormInputHidden(),
      'PRIORIDADPQR_ID' => new sfWidgetFormPropelChoice(array('model' => 'PrioridadPqr', 'add_empty' => false)),
      'CATEGORIAPQR_ID' => new sfWidgetFormPropelChoice(array('model' => 'CategoriaPqr', 'add_empty' => false)),
      'USUARIO_ID'      => new sfWidgetFormPropelChoice(array('model' => 'Usuario', 'add_empty' => false)),
      'ESTADOPQR_ID'    => new sfWidgetFormPropelChoice(array('model' => 'EstadoPqr', 'add_empty' => false)),
      'OBJETO'          => new sfWidgetFormInputText(),
      'CONTENIDO'       => new sfWidgetFormInputText(),
      'RUTA'            => new sfWidgetFormInputText(),
      'FECHA_CREACION'  => new sfWidgetFormDateTime(),
    ));

    $this->setValidators(array(
      'PQR_ID'          => new sfValidatorChoice(array('choices' => array($this->getObject()->getPqrId()), 'empty_value' => $this->getObject()->getPqrId(), 'required' => false)),
      'PRIORIDADPQR_ID' => new sfValidatorPropelChoice(array('model' => 'PrioridadPqr', 'column' => 'PRIORIDADPQR_ID')),
      'CATEGORIAPQR_ID' => new sfValidatorPropelChoice(array('model' => 'CategoriaPqr', 'column' => 'CATEGORIAPQR_ID')),
      'USUARIO_ID'      => new sfValidatorPropelChoice(array('model' => 'Usuario', 'column' => 'USUARIO_ID')),
      'ESTADOPQR_ID'    => new sfValidatorPropelChoice(array('model' => 'EstadoPqr', 'column' => 'ESTADOPQR_ID')),
      'OBJETO'          => new sfValidatorString(array('max_length' => 400, 'required' => false)),
      'CONTENIDO'       => new sfValidatorString(array('max_length' => 1000, 'required' => false)),
      'RUTA'            => new sfValidatorString(array('max_length' => 1000, 'required' => false)),
      'FECHA_CREACION'  => new sfValidatorDateTime(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('pqr[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'Pqr';
  }


}
