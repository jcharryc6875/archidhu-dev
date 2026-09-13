<?php

/**
 * UsUsHistorico filter form base class.
 *
 * @package    simad
 * @subpackage filter
 * @author     Your name here
 */
abstract class BaseUsUsHistoricoFormFilter extends BaseFormFilterPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'USUARIO_ID'        => new sfWidgetFormPropelChoice(array('model' => 'Usuario', 'add_empty' => true)),
      'HISTUSUARIO_ID'    => new sfWidgetFormPropelChoice(array('model' => 'HistUsuario', 'add_empty' => true)),
      'ROLUSHISTORICO_ID' => new sfWidgetFormPropelChoice(array('model' => 'RolUsHistorico', 'add_empty' => true)),
    ));

    $this->setValidators(array(
      'USUARIO_ID'        => new sfValidatorPropelChoice(array('required' => false, 'model' => 'Usuario', 'column' => 'USUARIO_ID')),
      'HISTUSUARIO_ID'    => new sfValidatorPropelChoice(array('required' => false, 'model' => 'HistUsuario', 'column' => 'HISTUSUARIO_ID')),
      'ROLUSHISTORICO_ID' => new sfValidatorPropelChoice(array('required' => false, 'model' => 'RolUsHistorico', 'column' => 'ROLUSHISTORICO_ID')),
    ));

    $this->widgetSchema->setNameFormat('us_us_historico_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'UsUsHistorico';
  }

  public function getFields()
  {
    return array(
      'USUSHISTORICO_ID'  => 'Number',
      'USUARIO_ID'        => 'ForeignKey',
      'HISTUSUARIO_ID'    => 'ForeignKey',
      'ROLUSHISTORICO_ID' => 'ForeignKey',
    );
  }
}
