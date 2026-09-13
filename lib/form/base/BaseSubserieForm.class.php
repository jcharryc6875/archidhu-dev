<?php

/**
 * Subserie form base class.
 *
 * @method Subserie getObject() Returns the current form's model object
 *
 * @package    ##PROJECT_NAME##
 * @subpackage form
 * @author     ##AUTHOR_NAME##
 */
abstract class BaseSubserieForm extends BaseFormPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'SUBSERIE_ID'                => new sfWidgetFormInputHidden(),
      'DESCARTEFINALSUBSERIE_ID'   => new sfWidgetFormPropelChoice(array('model' => 'DescarteFinalSubserie', 'add_empty' => false)),
      'SERIE_ID'                   => new sfWidgetFormPropelChoice(array('model' => 'Serie', 'add_empty' => false)),
      'CODIGO'                     => new sfWidgetFormInputText(),
      'DESCRIPCION'                => new sfWidgetFormInputText(),
      'ANOS_EN_GESTION'            => new sfWidgetFormInputText(),
      'ANOS_EN_CENTRAL'            => new sfWidgetFormInputText(),
      'ANOS_EN_HISTORICO'          => new sfWidgetFormInputText(),
      'ANOS_VALORACION_DOCUMENTAL' => new sfWidgetFormInputText(),
      'CODIGO2'                    => new sfWidgetFormInputText(),
      'PERIODO_SUBSERIE'           => new sfWidgetFormInputText(),
      'ES_MARCA'                   => new sfWidgetFormInputText(),
      'TIPOFIRMADIGITAL_ID'        => new sfWidgetFormInputText(),
      'PROCEDIMIENTO'              => new sfWidgetFormInputText(),
    ));

    $this->setValidators(array(
      'SUBSERIE_ID'                => new sfValidatorChoice(array('choices' => array($this->getObject()->getSubserieId()), 'empty_value' => $this->getObject()->getSubserieId(), 'required' => false)),
      'DESCARTEFINALSUBSERIE_ID'   => new sfValidatorPropelChoice(array('model' => 'DescarteFinalSubserie', 'column' => 'DESCARTEFINALSUBSERIE_ID')),
      'SERIE_ID'                   => new sfValidatorPropelChoice(array('model' => 'Serie', 'column' => 'SERIE_ID')),
      'CODIGO'                     => new sfValidatorString(array('max_length' => 50, 'required' => false)),
      'DESCRIPCION'                => new sfValidatorString(array('max_length' => 250, 'required' => false)),
      'ANOS_EN_GESTION'            => new sfValidatorInteger(array('min' => -32768, 'max' => 32767, 'required' => false)),
      'ANOS_EN_CENTRAL'            => new sfValidatorInteger(array('min' => -32768, 'max' => 32767, 'required' => false)),
      'ANOS_EN_HISTORICO'          => new sfValidatorInteger(array('min' => -32768, 'max' => 32767, 'required' => false)),
      'ANOS_VALORACION_DOCUMENTAL' => new sfValidatorInteger(array('min' => -32768, 'max' => 32767, 'required' => false)),
      'CODIGO2'                    => new sfValidatorString(array('max_length' => 50, 'required' => false)),
      'PERIODO_SUBSERIE'           => new sfValidatorString(array('max_length' => 50, 'required' => false)),
      'ES_MARCA'                   => new sfValidatorInteger(array('min' => -128, 'max' => 127, 'required' => false)),
      'TIPOFIRMADIGITAL_ID'        => new sfValidatorInteger(array('min' => -2147483648, 'max' => 2147483647, 'required' => false)),
      'PROCEDIMIENTO'              => new sfValidatorString(array('max_length' => 900, 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('subserie[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'Subserie';
  }


}
