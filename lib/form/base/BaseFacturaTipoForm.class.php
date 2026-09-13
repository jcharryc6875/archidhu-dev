<?php

/**
 * FacturaTipo form base class.
 *
 * @method FacturaTipo getObject() Returns the current form's model object
 *
 * @package    ##PROJECT_NAME##
 * @subpackage form
 * @author     ##AUTHOR_NAME##
 */
abstract class BaseFacturaTipoForm extends BaseFormPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'FACTURATIPO_ID' => new sfWidgetFormInputHidden(),
      'DESCRIPCION'    => new sfWidgetFormInputText(),
      'ES_VISIBLE'     => new sfWidgetFormInputText(),
      'CODIGO'         => new sfWidgetFormInputText(),
      'PARTIAL_NAME'   => new sfWidgetFormInputText(),
      'CHECKLIST_HTML' => new sfWidgetFormTextarea(),
    ));

    $this->setValidators(array(
      'FACTURATIPO_ID' => new sfValidatorChoice(array('choices' => array($this->getObject()->getFacturatipoId()), 'empty_value' => $this->getObject()->getFacturatipoId(), 'required' => false)),
      'DESCRIPCION'    => new sfValidatorString(array('max_length' => 50, 'required' => false)),
      'ES_VISIBLE'     => new sfValidatorInteger(array('min' => -128, 'max' => 127, 'required' => false)),
      'CODIGO'         => new sfValidatorString(array('max_length' => 10, 'required' => false)),
      'PARTIAL_NAME'   => new sfValidatorString(array('max_length' => 50, 'required' => false)),
      'CHECKLIST_HTML' => new sfValidatorString(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('factura_tipo[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'FacturaTipo';
  }


}
