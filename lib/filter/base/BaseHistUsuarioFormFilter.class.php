<?php

/**
 * HistUsuario filter form base class.
 *
 * @package    simad
 * @subpackage filter
 * @author     Your name here
 */
abstract class BaseHistUsuarioFormFilter extends BaseFormFilterPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'DEPENDENCIA_ID'      => new sfWidgetFormPropelChoice(array('model' => 'Dependencia', 'add_empty' => true)),
      'REGIONAL_ID'         => new sfWidgetFormPropelChoice(array('model' => 'Regional', 'add_empty' => true)),
      'CARGO_ID'            => new sfWidgetFormPropelChoice(array('model' => 'Cargo', 'add_empty' => true)),
      'ESTADOUSUARIO_ID'    => new sfWidgetFormPropelChoice(array('model' => 'EstadoUsuario', 'add_empty' => true)),
      'USER_NAME'           => new sfWidgetFormFilterInput(),
      'PASSWORD'            => new sfWidgetFormFilterInput(),
      'NOMBRE'              => new sfWidgetFormFilterInput(),
      'APELLIDO'            => new sfWidgetFormFilterInput(),
      'CEDULA'              => new sfWidgetFormFilterInput(),
      'EMAIL'               => new sfWidgetFormFilterInput(),
      'INICIALES'           => new sfWidgetFormFilterInput(),
      'EXTENSION'           => new sfWidgetFormFilterInput(),
      'INTENTOS'            => new sfWidgetFormFilterInput(),
      'PREFIJO'             => new sfWidgetFormFilterInput(),
      'FECHA_CREACION'      => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate())),
      'RUTA_FOTO'           => new sfWidgetFormFilterInput(),
      'FECHA_MODIFICACION'  => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate())),
      'SALT'                => new sfWidgetFormFilterInput(),
      'FECHA_ACTUALIZACION' => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate())),
      'USUARIO_AD'          => new sfWidgetFormFilterInput(),
    ));

    $this->setValidators(array(
      'DEPENDENCIA_ID'      => new sfValidatorPropelChoice(array('required' => false, 'model' => 'Dependencia', 'column' => 'DEPENDENCIA_ID')),
      'REGIONAL_ID'         => new sfValidatorPropelChoice(array('required' => false, 'model' => 'Regional', 'column' => 'REGIONAL_ID')),
      'CARGO_ID'            => new sfValidatorPropelChoice(array('required' => false, 'model' => 'Cargo', 'column' => 'CARGO_ID')),
      'ESTADOUSUARIO_ID'    => new sfValidatorPropelChoice(array('required' => false, 'model' => 'EstadoUsuario', 'column' => 'ESTADOUSUARIO_ID')),
      'USER_NAME'           => new sfValidatorPass(array('required' => false)),
      'PASSWORD'            => new sfValidatorPass(array('required' => false)),
      'NOMBRE'              => new sfValidatorPass(array('required' => false)),
      'APELLIDO'            => new sfValidatorPass(array('required' => false)),
      'CEDULA'              => new sfValidatorPass(array('required' => false)),
      'EMAIL'               => new sfValidatorPass(array('required' => false)),
      'INICIALES'           => new sfValidatorPass(array('required' => false)),
      'EXTENSION'           => new sfValidatorPass(array('required' => false)),
      'INTENTOS'            => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'PREFIJO'             => new sfValidatorPass(array('required' => false)),
      'FECHA_CREACION'      => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDate(array('required' => false)), 'to_date' => new sfValidatorDate(array('required' => false)))),
      'RUTA_FOTO'           => new sfValidatorPass(array('required' => false)),
      'FECHA_MODIFICACION'  => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDate(array('required' => false)), 'to_date' => new sfValidatorDate(array('required' => false)))),
      'SALT'                => new sfValidatorPass(array('required' => false)),
      'FECHA_ACTUALIZACION' => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDate(array('required' => false)), 'to_date' => new sfValidatorDate(array('required' => false)))),
      'USUARIO_AD'          => new sfValidatorPass(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('hist_usuario_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'HistUsuario';
  }

  public function getFields()
  {
    return array(
      'HISTUSUARIO_ID'      => 'Number',
      'DEPENDENCIA_ID'      => 'ForeignKey',
      'REGIONAL_ID'         => 'ForeignKey',
      'CARGO_ID'            => 'ForeignKey',
      'ESTADOUSUARIO_ID'    => 'ForeignKey',
      'USER_NAME'           => 'Text',
      'PASSWORD'            => 'Text',
      'NOMBRE'              => 'Text',
      'APELLIDO'            => 'Text',
      'CEDULA'              => 'Text',
      'EMAIL'               => 'Text',
      'INICIALES'           => 'Text',
      'EXTENSION'           => 'Text',
      'INTENTOS'            => 'Number',
      'PREFIJO'             => 'Text',
      'FECHA_CREACION'      => 'Date',
      'RUTA_FOTO'           => 'Text',
      'FECHA_MODIFICACION'  => 'Date',
      'SALT'                => 'Text',
      'FECHA_ACTUALIZACION' => 'Date',
      'USUARIO_AD'          => 'Text',
    );
  }
}
