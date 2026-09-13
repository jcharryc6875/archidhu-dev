<?php

/**
 * MarcaInterna form base class.
 *
 * @method MarcaInterna getObject() Returns the current form's model object
 *
 * @package    ##PROJECT_NAME##
 * @subpackage form
 * @author     ##AUTHOR_NAME##
 */
abstract class BaseMarcaInternaForm extends BaseFormPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'MARCAINTERNA_ID' => new sfWidgetFormInputHidden(),
      'COMINTERNA_ID'   => new sfWidgetFormPropelChoice(array('model' => 'ComInterna', 'add_empty' => false)),
      'USUARIO_ID'      => new sfWidgetFormPropelChoice(array('model' => 'Usuario', 'add_empty' => false)),
    ));

    $this->setValidators(array(
      'MARCAINTERNA_ID' => new sfValidatorChoice(array('choices' => array($this->getObject()->getMarcainternaId()), 'empty_value' => $this->getObject()->getMarcainternaId(), 'required' => false)),
      'COMINTERNA_ID'   => new sfValidatorPropelChoice(array('model' => 'ComInterna', 'column' => 'COMINTERNA_ID')),
      'USUARIO_ID'      => new sfValidatorPropelChoice(array('model' => 'Usuario', 'column' => 'USUARIO_ID')),
    ));

    $this->widgetSchema->setNameFormat('marca_interna[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'MarcaInterna';
  }


}
