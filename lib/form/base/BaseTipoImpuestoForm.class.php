<?php

/**
 * TipoImpuesto form base class.
 *
 * @method TipoImpuesto getObject() Returns the current form's model object
 *
 * @package    ##PROJECT_NAME##
 * @subpackage form
 * @author     ##AUTHOR_NAME##
 */
abstract class BaseTipoImpuestoForm extends BaseFormPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'TIPOIMPUESTO_ID' => new sfWidgetFormInputHidden(),
      'DESCRIPCION'     => new sfWidgetFormInputText(),
    ));

    $this->setValidators(array(
      'TIPOIMPUESTO_ID' => new sfValidatorChoice(array('choices' => array($this->getObject()->getTipoimpuestoId()), 'empty_value' => $this->getObject()->getTipoimpuestoId(), 'required' => false)),
      'DESCRIPCION'     => new sfValidatorString(array('max_length' => 100, 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('tipo_impuesto[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'TipoImpuesto';
  }


}
