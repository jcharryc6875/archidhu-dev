<?php

/**
 * OficinaProductora form base class.
 *
 * @method OficinaProductora getObject() Returns the current form's model object
 *
 * @package    ##PROJECT_NAME##
 * @subpackage form
 * @author     ##AUTHOR_NAME##
 */
abstract class BaseOficinaProductoraForm extends BaseFormPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'OFICINAPRODUCTORA_ID' => new sfWidgetFormInputHidden(),
      'PROCESOS_ID'          => new sfWidgetFormPropelChoice(array('model' => 'Procesos', 'add_empty' => false)),
      'DESCRIPCION'          => new sfWidgetFormInputText(),
      'CODIGO'               => new sfWidgetFormInputText(),
    ));

    $this->setValidators(array(
      'OFICINAPRODUCTORA_ID' => new sfValidatorChoice(array('choices' => array($this->getObject()->getOficinaproductoraId()), 'empty_value' => $this->getObject()->getOficinaproductoraId(), 'required' => false)),
      'PROCESOS_ID'          => new sfValidatorPropelChoice(array('model' => 'Procesos', 'column' => 'PROCESOS_ID')),
      'DESCRIPCION'          => new sfValidatorString(array('max_length' => 250, 'required' => false)),
      'CODIGO'               => new sfValidatorString(array('max_length' => 50, 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('oficina_productora[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'OficinaProductora';
  }


}
