<?php

/**
 * AutorizacionFirma form base class.
 *
 * @method AutorizacionFirma getObject() Returns the current form's model object
 *
 * @package    ##PROJECT_NAME##
 * @subpackage form
 * @author     ##AUTHOR_NAME##
 */
abstract class BaseAutorizacionFirmaForm extends BaseFormPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'AUTORIZACIONFIRMA_ID' => new sfWidgetFormInputHidden(),
      'ESTADOFIRMAAUTO_ID'   => new sfWidgetFormPropelChoice(array('model' => 'EstadoFirmaAuto', 'add_empty' => true)),
      'USUARIO_ID'           => new sfWidgetFormPropelChoice(array('model' => 'Usuario', 'add_empty' => true)),
      'MODULO_ID'            => new sfWidgetFormInputText(),
      'FIRMA_ELECTRONICA'    => new sfWidgetFormInputText(),
      'FIRMA_DIGITAL'        => new sfWidgetFormInputText(),
      'FIRMA_FISICA'         => new sfWidgetFormInputText(),
    ));

    $this->setValidators(array(
      'AUTORIZACIONFIRMA_ID' => new sfValidatorChoice(array('choices' => array($this->getObject()->getAutorizacionfirmaId()), 'empty_value' => $this->getObject()->getAutorizacionfirmaId(), 'required' => false)),
      'ESTADOFIRMAAUTO_ID'   => new sfValidatorPropelChoice(array('model' => 'EstadoFirmaAuto', 'column' => 'ESTADOFIRMAAUTO_ID', 'required' => false)),
      'USUARIO_ID'           => new sfValidatorPropelChoice(array('model' => 'Usuario', 'column' => 'USUARIO_ID', 'required' => false)),
      'MODULO_ID'            => new sfValidatorInteger(array('min' => -2147483648, 'max' => 2147483647, 'required' => false)),
      'FIRMA_ELECTRONICA'    => new sfValidatorInteger(array('min' => -128, 'max' => 127, 'required' => false)),
      'FIRMA_DIGITAL'        => new sfValidatorInteger(array('min' => -128, 'max' => 127, 'required' => false)),
      'FIRMA_FISICA'         => new sfValidatorInteger(array('min' => -128, 'max' => 127, 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('autorizacion_firma[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'AutorizacionFirma';
  }


}
