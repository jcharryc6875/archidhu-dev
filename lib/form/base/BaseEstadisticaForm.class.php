<?php

/**
 * Estadistica form base class.
 *
 * @method Estadistica getObject() Returns the current form's model object
 *
 * @package    ##PROJECT_NAME##
 * @subpackage form
 * @author     ##AUTHOR_NAME##
 */
abstract class BaseEstadisticaForm extends BaseFormPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'ESTADISTICA_ID' => new sfWidgetFormInputHidden(),
      'ORDEN'          => new sfWidgetFormInputText(),
      'MODULO'         => new sfWidgetFormInputText(),
      'NOMBRE'         => new sfWidgetFormInputText(),
      'DESCRIPCION'    => new sfWidgetFormInputText(),
      'CODIGOSQL'      => new sfWidgetFormTextarea(),
    ));

    $this->setValidators(array(
      'ESTADISTICA_ID' => new sfValidatorChoice(array('choices' => array($this->getObject()->getEstadisticaId()), 'empty_value' => $this->getObject()->getEstadisticaId(), 'required' => false)),
      'ORDEN'          => new sfValidatorInteger(array('min' => -32768, 'max' => 32767, 'required' => false)),
      'MODULO'         => new sfValidatorString(array('max_length' => 30, 'required' => false)),
      'NOMBRE'         => new sfValidatorString(array('max_length' => 100, 'required' => false)),
      'DESCRIPCION'    => new sfValidatorString(array('max_length' => 250, 'required' => false)),
      'CODIGOSQL'      => new sfValidatorString(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('estadistica[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'Estadistica';
  }


}
