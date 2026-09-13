<?php

/**
 * ProvGrupoTesoreria form base class.
 *
 * @method ProvGrupoTesoreria getObject() Returns the current form's model object
 *
 * @package    ##PROJECT_NAME##
 * @subpackage form
 * @author     ##AUTHOR_NAME##
 */
abstract class BaseProvGrupoTesoreriaForm extends BaseFormPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'PROV_GRUPO_TESORERIA_ID' => new sfWidgetFormInputHidden(),
      'PAIS_ID'                 => new sfWidgetFormPropelChoice(array('model' => 'Pais', 'add_empty' => false)),
      'DESCRIPCION'             => new sfWidgetFormInputText(),
      'CODIGO'                  => new sfWidgetFormInputText(),
    ));

    $this->setValidators(array(
      'PROV_GRUPO_TESORERIA_ID' => new sfValidatorChoice(array('choices' => array($this->getObject()->getProvGrupoTesoreriaId()), 'empty_value' => $this->getObject()->getProvGrupoTesoreriaId(), 'required' => false)),
      'PAIS_ID'                 => new sfValidatorPropelChoice(array('model' => 'Pais', 'column' => 'PAIS_ID')),
      'DESCRIPCION'             => new sfValidatorString(array('max_length' => 250, 'required' => false)),
      'CODIGO'                  => new sfValidatorString(array('max_length' => 50, 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('prov_grupo_tesoreria[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'ProvGrupoTesoreria';
  }


}
