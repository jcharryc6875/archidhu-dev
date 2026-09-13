<?php

/**
 * Serie form base class.
 *
 * @method Serie getObject() Returns the current form's model object
 *
 * @package    ##PROJECT_NAME##
 * @subpackage form
 * @author     ##AUTHOR_NAME##
 */
abstract class BaseSerieForm extends BaseFormPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'SERIE_ID'         => new sfWidgetFormInputHidden(),
      'DEPENDENCIA_ID'   => new sfWidgetFormPropelChoice(array('model' => 'Dependencia', 'add_empty' => false)),
      'CODIGO'           => new sfWidgetFormInputText(),
      'DESCRIPCION'      => new sfWidgetFormInputText(),
      'ES_IMPORTACIONES' => new sfWidgetFormInputText(),
    ));

    $this->setValidators(array(
      'SERIE_ID'         => new sfValidatorChoice(array('choices' => array($this->getObject()->getSerieId()), 'empty_value' => $this->getObject()->getSerieId(), 'required' => false)),
      'DEPENDENCIA_ID'   => new sfValidatorPropelChoice(array('model' => 'Dependencia', 'column' => 'DEPENDENCIA_ID')),
      'CODIGO'           => new sfValidatorString(array('max_length' => 50, 'required' => false)),
      'DESCRIPCION'      => new sfValidatorString(array('max_length' => 250, 'required' => false)),
      'ES_IMPORTACIONES' => new sfValidatorInteger(array('min' => -128, 'max' => 127, 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('serie[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'Serie';
  }


}
