<?php

/**
 * ComenviadaMasivas form base class.
 *
 * @method ComenviadaMasivas getObject() Returns the current form's model object
 *
 * @package    ##PROJECT_NAME##
 * @subpackage form
 * @author     ##AUTHOR_NAME##
 */
abstract class BaseComenviadaMasivasForm extends BaseFormPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'COMENVIADAMASIVAS_ID' => new sfWidgetFormInputHidden(),
      'USUARIO_ID'           => new sfWidgetFormPropelChoice(array('model' => 'Usuario', 'add_empty' => false)),
      'COMRECIBIDA_ID'       => new sfWidgetFormPropelChoice(array('model' => 'ComRecibida', 'add_empty' => false)),
      'COMENVIADA_ID'        => new sfWidgetFormPropelChoice(array('model' => 'ComEnviada', 'add_empty' => true)),
      'PLANTILLASCOM_ID'     => new sfWidgetFormPropelChoice(array('model' => 'PlantillasCom', 'add_empty' => false)),
      'MARCA'                => new sfWidgetFormInputText(),
      'ESTADO_PROCESO'       => new sfWidgetFormInputText(),
      'MSG_PROCESO'          => new sfWidgetFormInputText(),
      'USUARIOS_FIRMAS'      => new sfWidgetFormInputText(),
      'CARGOS_FIRMAS'        => new sfWidgetFormInputText(),
      'FECHA_CREACION'       => new sfWidgetFormDateTime(),
      'FECHA_EJECUCION'      => new sfWidgetFormDateTime(),
      'CONTENIDO_TEXT'       => new sfWidgetFormTextarea(),
      'SEND_EMAIL'           => new sfWidgetFormInputText(),
      'GENERATE_FILE'        => new sfWidgetFormInputText(),
      'RADICADO_RESPUESTA'   => new sfWidgetFormInputText(),
    ));

    $this->setValidators(array(
      'COMENVIADAMASIVAS_ID' => new sfValidatorChoice(array('choices' => array($this->getObject()->getComenviadamasivasId()), 'empty_value' => $this->getObject()->getComenviadamasivasId(), 'required' => false)),
      'USUARIO_ID'           => new sfValidatorPropelChoice(array('model' => 'Usuario', 'column' => 'USUARIO_ID')),
      'COMRECIBIDA_ID'       => new sfValidatorPropelChoice(array('model' => 'ComRecibida', 'column' => 'COMRECIBIDA_ID')),
      'COMENVIADA_ID'        => new sfValidatorPropelChoice(array('model' => 'ComEnviada', 'column' => 'COMENVIADA_ID', 'required' => false)),
      'PLANTILLASCOM_ID'     => new sfValidatorPropelChoice(array('model' => 'PlantillasCom', 'column' => 'PLANTILLASCOM_ID')),
      'MARCA'                => new sfValidatorInteger(array('min' => -2147483648, 'max' => 2147483647, 'required' => false)),
      'ESTADO_PROCESO'       => new sfValidatorInteger(array('min' => -2147483648, 'max' => 2147483647, 'required' => false)),
      'MSG_PROCESO'          => new sfValidatorString(array('max_length' => 500, 'required' => false)),
      'USUARIOS_FIRMAS'      => new sfValidatorString(array('max_length' => 500, 'required' => false)),
      'CARGOS_FIRMAS'        => new sfValidatorString(array('max_length' => 500, 'required' => false)),
      'FECHA_CREACION'       => new sfValidatorDateTime(array('required' => false)),
      'FECHA_EJECUCION'      => new sfValidatorDateTime(array('required' => false)),
      'CONTENIDO_TEXT'       => new sfValidatorString(array('required' => false)),
      'SEND_EMAIL'           => new sfValidatorInteger(array('min' => -128, 'max' => 127, 'required' => false)),
      'GENERATE_FILE'        => new sfValidatorInteger(array('min' => -128, 'max' => 127, 'required' => false)),
      'RADICADO_RESPUESTA'   => new sfValidatorString(array('max_length' => 50, 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('comenviada_masivas[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'ComenviadaMasivas';
  }


}
