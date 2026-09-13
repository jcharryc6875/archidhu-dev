<?php

/**
 * NotaPqr form base class.
 *
 * @method NotaPqr getObject() Returns the current form's model object
 *
 * @package    ##PROJECT_NAME##
 * @subpackage form
 * @author     ##AUTHOR_NAME##
 */
abstract class BaseNotaPqrForm extends BaseFormPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'NOTAPQR_ID' => new sfWidgetFormInputHidden(),
      'PQR_ID'     => new sfWidgetFormPropelChoice(array('model' => 'Pqr', 'add_empty' => false)),
      'USUARIO_ID' => new sfWidgetFormPropelChoice(array('model' => 'Usuario', 'add_empty' => false)),
      'CONTENIDO'  => new sfWidgetFormInputText(),
      'RUTA'       => new sfWidgetFormInputText(),
      'FECHA'      => new sfWidgetFormDateTime(),
    ));

    $this->setValidators(array(
      'NOTAPQR_ID' => new sfValidatorChoice(array('choices' => array($this->getObject()->getNotapqrId()), 'empty_value' => $this->getObject()->getNotapqrId(), 'required' => false)),
      'PQR_ID'     => new sfValidatorPropelChoice(array('model' => 'Pqr', 'column' => 'PQR_ID')),
      'USUARIO_ID' => new sfValidatorPropelChoice(array('model' => 'Usuario', 'column' => 'USUARIO_ID')),
      'CONTENIDO'  => new sfValidatorString(array('max_length' => 1000, 'required' => false)),
      'RUTA'       => new sfValidatorString(array('max_length' => 1000, 'required' => false)),
      'FECHA'      => new sfValidatorDateTime(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('nota_pqr[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'NotaPqr';
  }


}
