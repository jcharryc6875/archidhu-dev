<?php

/**
 * ProvGrupoEsquema form base class.
 *
 * @method ProvGrupoEsquema getObject() Returns the current form's model object
 *
 * @package    ##PROJECT_NAME##
 * @subpackage form
 * @author     ##AUTHOR_NAME##
 */
abstract class BaseProvGrupoEsquemaForm extends BaseFormPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'PROV_GRUPO_ESQUEMA_ID' => new sfWidgetFormInputHidden(),
      'CODIGO'                => new sfWidgetFormInputText(),
      'DESCRIPCION'           => new sfWidgetFormInputText(),
    ));

    $this->setValidators(array(
      'PROV_GRUPO_ESQUEMA_ID' => new sfValidatorChoice(array('choices' => array($this->getObject()->getProvGrupoEsquemaId()), 'empty_value' => $this->getObject()->getProvGrupoEsquemaId(), 'required' => false)),
      'CODIGO'                => new sfValidatorString(array('max_length' => 50, 'required' => false)),
      'DESCRIPCION'           => new sfValidatorString(array('max_length' => 250, 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('prov_grupo_esquema[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'ProvGrupoEsquema';
  }


}
