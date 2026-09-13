<?php

/**
 * Vinculada form base class.
 *
 * @method Vinculada getObject() Returns the current form's model object
 *
 * @package    ##PROJECT_NAME##
 * @subpackage form
 * @author     ##AUTHOR_NAME##
 */
abstract class BaseVinculadaForm extends BaseFormPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'VINCULADA_ID'   => new sfWidgetFormInputHidden(),
      'USUARIO_ID'     => new sfWidgetFormPropelChoice(array('model' => 'Usuario', 'add_empty' => false)),
      'DESCRIPCION'    => new sfWidgetFormInputText(),
      'RUTA'           => new sfWidgetFormInputText(),
      'FECHA_CREACION' => new sfWidgetFormDateTime(),
      'FOLIOS'         => new sfWidgetFormInputText(),
      'ACEPTADO'       => new sfWidgetFormInputText(),
    ));

    $this->setValidators(array(
      'VINCULADA_ID'   => new sfValidatorChoice(array('choices' => array($this->getObject()->getVinculadaId()), 'empty_value' => $this->getObject()->getVinculadaId(), 'required' => false)),
      'USUARIO_ID'     => new sfValidatorPropelChoice(array('model' => 'Usuario', 'column' => 'USUARIO_ID')),
      'DESCRIPCION'    => new sfValidatorString(array('max_length' => 250, 'required' => false)),
      'RUTA'           => new sfValidatorString(array('max_length' => 1000, 'required' => false)),
      'FECHA_CREACION' => new sfValidatorDateTime(array('required' => false)),
      'FOLIOS'         => new sfValidatorInteger(array('min' => -2147483648, 'max' => 2147483647, 'required' => false)),
      'ACEPTADO'       => new sfValidatorInteger(array('min' => -128, 'max' => 127, 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('vinculada[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'Vinculada';
  }


}
