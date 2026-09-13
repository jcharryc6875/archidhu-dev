<?php

/**
 * UnidadAdministrativa form base class.
 *
 * @package    form
 * @subpackage unidad_administrativa
 * @version    SVN: $Id: sfPropelFormGeneratedTemplate.php 15484 2009-02-13 13:13:51Z fabien $
 */
class BaseUnidadAdministrativaForm extends BaseFormPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'unidadadministrativa_id' => new sfWidgetFormInputHidden(),
      'procesos_id'             => new sfWidgetFormPropelSelect(array('model' => 'Procesos', 'add_empty' => false)),
      'descripcion'             => new sfWidgetFormInputText(),
    ));

    $this->setValidators(array(
      'unidadadministrativa_id' => new sfValidatorPropelChoice(array('model' => 'UnidadAdministrativa', 'column' => 'unidadadministrativa_id', 'required' => false)),
      'procesos_id'             => new sfValidatorPropelChoice(array('model' => 'Procesos', 'column' => 'procesos_id')),
      'descripcion'             => new sfValidatorString(array('max_length' => 250, 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('unidad_administrativa[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'UnidadAdministrativa';
  }


}
