<?php

/**
 * EstadoFirmaAuto form base class.
 *
 * @method EstadoFirmaAuto getObject() Returns the current form's model object
 *
 * @package    ##PROJECT_NAME##
 * @subpackage form
 * @author     ##AUTHOR_NAME##
 */
abstract class BaseEstadoFirmaAutoForm extends BaseFormPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'ESTADOFIRMAAUTO_ID' => new sfWidgetFormInputHidden(),
      'DESCRIPCION'        => new sfWidgetFormInputText(),
    ));

    $this->setValidators(array(
      'ESTADOFIRMAAUTO_ID' => new sfValidatorChoice(array('choices' => array($this->getObject()->getEstadofirmaautoId()), 'empty_value' => $this->getObject()->getEstadofirmaautoId(), 'required' => false)),
      'DESCRIPCION'        => new sfValidatorString(array('max_length' => 50, 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('estado_firma_auto[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'EstadoFirmaAuto';
  }


}
