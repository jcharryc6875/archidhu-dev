<?php

/**
 * TipoDocumental form base class.
 *
 * @method TipoDocumental getObject() Returns the current form's model object
 *
 * @package    ##PROJECT_NAME##
 * @subpackage form
 * @author     ##AUTHOR_NAME##
 */
abstract class BaseTipoDocumentalForm extends BaseFormPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'TIPODOCUMENTAL_ID' => new sfWidgetFormInputHidden(),
      'SUBSERIE_ID'       => new sfWidgetFormPropelChoice(array('model' => 'Subserie', 'add_empty' => false)),
      'CODIGO'            => new sfWidgetFormInputText(),
      'DESCRIPCION'       => new sfWidgetFormInputText(),
      'ORDEN'             => new sfWidgetFormInputText(),
      'ES_FORMATO'        => new sfWidgetFormInputText(),
    ));

    $this->setValidators(array(
      'TIPODOCUMENTAL_ID' => new sfValidatorChoice(array('choices' => array($this->getObject()->getTipodocumentalId()), 'empty_value' => $this->getObject()->getTipodocumentalId(), 'required' => false)),
      'SUBSERIE_ID'       => new sfValidatorPropelChoice(array('model' => 'Subserie', 'column' => 'SUBSERIE_ID')),
      'CODIGO'            => new sfValidatorString(array('max_length' => 50, 'required' => false)),
      'DESCRIPCION'       => new sfValidatorString(array('max_length' => 250, 'required' => false)),
      'ORDEN'             => new sfValidatorInteger(array('min' => -32768, 'max' => 32767, 'required' => false)),
      'ES_FORMATO'        => new sfValidatorInteger(array('min' => -128, 'max' => 127, 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('tipo_documental[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'TipoDocumental';
  }


}
