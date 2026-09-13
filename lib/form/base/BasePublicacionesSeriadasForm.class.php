<?php

/**
 * PublicacionesSeriadas form base class.
 *
 * @method PublicacionesSeriadas getObject() Returns the current form's model object
 *
 * @package    ##PROJECT_NAME##
 * @subpackage form
 * @author     ##AUTHOR_NAME##
 */
abstract class BasePublicacionesSeriadasForm extends BaseFormPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'PUBLICACIONESSERIADAS_ID' => new sfWidgetFormInputHidden(),
      'DOCUMENTACION_ID'         => new sfWidgetFormPropelChoice(array('model' => 'Documentacion', 'add_empty' => false)),
      'MENCION_EDICION'          => new sfWidgetFormInputText(),
      'VERSION'                  => new sfWidgetFormInputText(),
      'PAGINACION'               => new sfWidgetFormInputText(),
      'ILUSTRACIONES'            => new sfWidgetFormInputText(),
      'VOLUMEN'                  => new sfWidgetFormInputText(),
      'MATERIAL_COMPLEMENTARIO'  => new sfWidgetFormInputText(),
      'ISSN'                     => new sfWidgetFormInputText(),
      'NOTAS_CONTENIDO'          => new sfWidgetFormInputText(),
    ));

    $this->setValidators(array(
      'PUBLICACIONESSERIADAS_ID' => new sfValidatorChoice(array('choices' => array($this->getObject()->getPublicacionesseriadasId()), 'empty_value' => $this->getObject()->getPublicacionesseriadasId(), 'required' => false)),
      'DOCUMENTACION_ID'         => new sfValidatorPropelChoice(array('model' => 'Documentacion', 'column' => 'DOCUMENTACION_ID')),
      'MENCION_EDICION'          => new sfValidatorString(array('max_length' => 30, 'required' => false)),
      'VERSION'                  => new sfValidatorNumber(array('required' => false)),
      'PAGINACION'               => new sfValidatorInteger(array('min' => -2147483648, 'max' => 2147483647, 'required' => false)),
      'ILUSTRACIONES'            => new sfValidatorInteger(array('min' => -128, 'max' => 127, 'required' => false)),
      'VOLUMEN'                  => new sfValidatorNumber(array('required' => false)),
      'MATERIAL_COMPLEMENTARIO'  => new sfValidatorString(array('max_length' => 30, 'required' => false)),
      'ISSN'                     => new sfValidatorString(array('max_length' => 30, 'required' => false)),
      'NOTAS_CONTENIDO'          => new sfValidatorString(array('max_length' => 50, 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('publicaciones_seriadas[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'PublicacionesSeriadas';
  }


}
