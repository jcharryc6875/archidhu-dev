<?php

/**
 * ProvUsuarioArea form base class.
 *
 * @method ProvUsuarioArea getObject() Returns the current form's model object
 *
 * @package    ##PROJECT_NAME##
 * @subpackage form
 * @author     ##AUTHOR_NAME##
 */
abstract class BaseProvUsuarioAreaForm extends BaseFormPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'PROV_USUARIO_AREA_ID'     => new sfWidgetFormInputHidden(),
      'PROV_ESTADO_APROBADOR_ID' => new sfWidgetFormPropelChoice(array('model' => 'ProvEstadoAprobador', 'add_empty' => false)),
      'USUARIO_ID'               => new sfWidgetFormPropelChoice(array('model' => 'Usuario', 'add_empty' => false)),
      'PAIS_ID'                  => new sfWidgetFormPropelChoice(array('model' => 'Pais', 'add_empty' => false)),
      'PROV_AREA_APROBADORA_ID'  => new sfWidgetFormPropelChoice(array('model' => 'ProvAreaAprobadora', 'add_empty' => false)),
    ));

    $this->setValidators(array(
      'PROV_USUARIO_AREA_ID'     => new sfValidatorChoice(array('choices' => array($this->getObject()->getProvUsuarioAreaId()), 'empty_value' => $this->getObject()->getProvUsuarioAreaId(), 'required' => false)),
      'PROV_ESTADO_APROBADOR_ID' => new sfValidatorPropelChoice(array('model' => 'ProvEstadoAprobador', 'column' => 'PROV_ESTADO_APROBADOR_ID')),
      'USUARIO_ID'               => new sfValidatorPropelChoice(array('model' => 'Usuario', 'column' => 'USUARIO_ID')),
      'PAIS_ID'                  => new sfValidatorPropelChoice(array('model' => 'Pais', 'column' => 'PAIS_ID')),
      'PROV_AREA_APROBADORA_ID'  => new sfValidatorPropelChoice(array('model' => 'ProvAreaAprobadora', 'column' => 'PROV_AREA_APROBADORA_ID')),
    ));

    $this->widgetSchema->setNameFormat('prov_usuario_area[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'ProvUsuarioArea';
  }


}
