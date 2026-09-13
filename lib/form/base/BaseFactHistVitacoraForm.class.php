<?php

/**
 * FactHistVitacora form base class.
 *
 * @method FactHistVitacora getObject() Returns the current form's model object
 *
 * @package    ##PROJECT_NAME##
 * @subpackage form
 * @author     ##AUTHOR_NAME##
 */
abstract class BaseFactHistVitacoraForm extends BaseFormPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'FACTHISTVITACORA_ID' => new sfWidgetFormInputHidden(),
      'FACTHIST_ID'         => new sfWidgetFormPropelChoice(array('model' => 'FactHist', 'add_empty' => false)),
      'ESTADO'              => new sfWidgetFormInputText(),
      'FECHA'               => new sfWidgetFormDateTime(),
      'ORIGEN'              => new sfWidgetFormInputText(),
      'DESTINO'             => new sfWidgetFormInputText(),
      'ESTADO_ID'           => new sfWidgetFormInputText(),
      'ORIGEN_ID'           => new sfWidgetFormInputText(),
      'DESTINO_ID'          => new sfWidgetFormInputText(),
      'OBSERVACIONES'       => new sfWidgetFormInputText(),
    ));

    $this->setValidators(array(
      'FACTHISTVITACORA_ID' => new sfValidatorChoice(array('choices' => array($this->getObject()->getFacthistvitacoraId()), 'empty_value' => $this->getObject()->getFacthistvitacoraId(), 'required' => false)),
      'FACTHIST_ID'         => new sfValidatorPropelChoice(array('model' => 'FactHist', 'column' => 'FACTHIST_ID')),
      'ESTADO'              => new sfValidatorString(array('max_length' => 150, 'required' => false)),
      'FECHA'               => new sfValidatorDateTime(array('required' => false)),
      'ORIGEN'              => new sfValidatorString(array('max_length' => 100, 'required' => false)),
      'DESTINO'             => new sfValidatorString(array('max_length' => 100, 'required' => false)),
      'ESTADO_ID'           => new sfValidatorInteger(array('min' => -2147483648, 'max' => 2147483647, 'required' => false)),
      'ORIGEN_ID'           => new sfValidatorInteger(array('min' => -2147483648, 'max' => 2147483647, 'required' => false)),
      'DESTINO_ID'          => new sfValidatorInteger(array('min' => -2147483648, 'max' => 2147483647, 'required' => false)),
      'OBSERVACIONES'       => new sfValidatorString(array('max_length' => 500, 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('fact_hist_vitacora[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'FactHistVitacora';
  }


}
