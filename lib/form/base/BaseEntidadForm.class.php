<?php

/**
 * Entidad form base class.
 *
 * @method Entidad getObject() Returns the current form's model object
 *
 * @package    ##PROJECT_NAME##
 * @subpackage form
 * @author     ##AUTHOR_NAME##
 */
abstract class BaseEntidadForm extends BaseFormPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'ENTIDAD_ID'       => new sfWidgetFormInputHidden(),
      'DESCRIPCION'      => new sfWidgetFormInputText(),
      'DIRECTORIO_NAME'  => new sfWidgetFormInputText(),
      'ES_ACTUAL'        => new sfWidgetFormInputText(),
      'CODIGO'           => new sfWidgetFormInputText(),
      'LOGO_HEADER'      => new sfWidgetFormInputText(),
      'LOGO_CORPORATIVO' => new sfWidgetFormInputText(),
      'USAR_MEMBRETE'    => new sfWidgetFormInputText(),
    ));

    $this->setValidators(array(
      'ENTIDAD_ID'       => new sfValidatorChoice(array('choices' => array($this->getObject()->getEntidadId()), 'empty_value' => $this->getObject()->getEntidadId(), 'required' => false)),
      'DESCRIPCION'      => new sfValidatorString(array('max_length' => 250, 'required' => false)),
      'DIRECTORIO_NAME'  => new sfValidatorString(array('max_length' => 50, 'required' => false)),
      'ES_ACTUAL'        => new sfValidatorInteger(array('min' => -128, 'max' => 127, 'required' => false)),
      'CODIGO'           => new sfValidatorString(array('max_length' => 10, 'required' => false)),
      'LOGO_HEADER'      => new sfValidatorString(array('max_length' => 500, 'required' => false)),
      'LOGO_CORPORATIVO' => new sfValidatorString(array('max_length' => 30, 'required' => false)),
      'USAR_MEMBRETE'    => new sfValidatorInteger(array('min' => -128, 'max' => 127, 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('entidad[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'Entidad';
  }


}
