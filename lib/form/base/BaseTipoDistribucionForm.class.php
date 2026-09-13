<?php

/**
 * TipoDistribucion form base class.
 *
 * @method TipoDistribucion getObject() Returns the current form's model object
 *
 * @package    ##PROJECT_NAME##
 * @subpackage form
 * @author     ##AUTHOR_NAME##
 */
abstract class BaseTipoDistribucionForm extends BaseFormPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'TIPODISTRIBUCION_ID' => new sfWidgetFormInputHidden(),
      'DESCRIPCION'         => new sfWidgetFormInputText(),
    ));

    $this->setValidators(array(
      'TIPODISTRIBUCION_ID' => new sfValidatorChoice(array('choices' => array($this->getObject()->getTipodistribucionId()), 'empty_value' => $this->getObject()->getTipodistribucionId(), 'required' => false)),
      'DESCRIPCION'         => new sfValidatorString(array('max_length' => 50, 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('tipo_distribucion[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'TipoDistribucion';
  }


}
