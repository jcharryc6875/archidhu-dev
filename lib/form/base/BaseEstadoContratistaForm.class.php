<?php

/**
 * EstadoContratista form base class.
 *
 * @method EstadoContratista getObject() Returns the current form's model object
 *
 * @package    ##PROJECT_NAME##
 * @subpackage form
 * @author     ##AUTHOR_NAME##
 */
abstract class BaseEstadoContratistaForm extends BaseFormPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'ESTADOCONTRATISTA_ID' => new sfWidgetFormInputHidden(),
      'DESCRIPCION'          => new sfWidgetFormInputText(),
    ));

    $this->setValidators(array(
      'ESTADOCONTRATISTA_ID' => new sfValidatorChoice(array('choices' => array($this->getObject()->getEstadocontratistaId()), 'empty_value' => $this->getObject()->getEstadocontratistaId(), 'required' => false)),
      'DESCRIPCION'          => new sfValidatorString(array('max_length' => 50, 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('estado_contratista[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'EstadoContratista';
  }


}
