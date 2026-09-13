<?php

/**
 * TipoServicio form base class.
 *
 * @method TipoServicio getObject() Returns the current form's model object
 *
 * @package    ##PROJECT_NAME##
 * @subpackage form
 * @author     ##AUTHOR_NAME##
 */
abstract class BaseTipoServicioForm extends BaseFormPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'TIPOSERVICIO_ID' => new sfWidgetFormInputHidden(),
      'ENTIDAD_ID'      => new sfWidgetFormPropelChoice(array('model' => 'Entidad', 'add_empty' => true)),
      'DESCRIPCION'     => new sfWidgetFormInputText(),
      'ES_VISIBLE'      => new sfWidgetFormInputText(),
    ));

    $this->setValidators(array(
      'TIPOSERVICIO_ID' => new sfValidatorChoice(array('choices' => array($this->getObject()->getTiposervicioId()), 'empty_value' => $this->getObject()->getTiposervicioId(), 'required' => false)),
      'ENTIDAD_ID'      => new sfValidatorPropelChoice(array('model' => 'Entidad', 'column' => 'ENTIDAD_ID', 'required' => false)),
      'DESCRIPCION'     => new sfValidatorString(array('max_length' => 250, 'required' => false)),
      'ES_VISIBLE'      => new sfValidatorInteger(array('min' => -2147483648, 'max' => 2147483647, 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('tipo_servicio[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'TipoServicio';
  }


}
