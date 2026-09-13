<?php

/**
 * ControlContratistas filter form base class.
 *
 * @package    simad
 * @subpackage filter
 * @author     Your name here
 */
abstract class BaseControlContratistasFormFilter extends BaseFormFilterPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'ESTADOCONTRATISTA_ID'      => new sfWidgetFormPropelChoice(array('model' => 'EstadoContratista', 'add_empty' => true)),
      'TIPOIDENTIFICACION_ID'     => new sfWidgetFormPropelChoice(array('model' => 'TipoIdentificacion', 'add_empty' => true)),
      'USUARIO_ID'                => new sfWidgetFormPropelChoice(array('model' => 'Usuario', 'add_empty' => true)),
      'CIUDAD_ID'                 => new sfWidgetFormPropelChoice(array('model' => 'Ciudad', 'add_empty' => true)),
      'DEPENDENCIA_ID'            => new sfWidgetFormPropelChoice(array('model' => 'Dependencia', 'add_empty' => true)),
      'CARGO_ID'                  => new sfWidgetFormPropelChoice(array('model' => 'Cargo', 'add_empty' => true)),
      'TIPOCONTRATO_ID'           => new sfWidgetFormPropelChoice(array('model' => 'TipoContrato', 'add_empty' => true)),
      'NUMERO_IDENTIFICACION'     => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'NOMBRE'                    => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'PRIMER_APELLIDO'           => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'SEGUNDO_APELLIDO'          => new sfWidgetFormFilterInput(),
      'GENERO'                    => new sfWidgetFormFilterInput(),
      'NACIONALIDAD'              => new sfWidgetFormFilterInput(),
      'LIBRETA_MILITAR'           => new sfWidgetFormFilterInput(),
      'FECHA_NACIMIENTO'          => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate())),
      'DIRECCION_ENVIO'           => new sfWidgetFormFilterInput(),
      'EMAIL'                     => new sfWidgetFormFilterInput(),
      'EDUCACION_BASICA'          => new sfWidgetFormFilterInput(),
      'FECHA_GRADO_BASICA'        => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate())),
      'EDUCACION_SUPERIOR'        => new sfWidgetFormFilterInput(),
      'EXPERIENCIA_LABORAL'       => new sfWidgetFormFilterInput(),
      'EPS'                       => new sfWidgetFormFilterInput(),
      'FONDO_PENSIONES'           => new sfWidgetFormFilterInput(),
      'FECHA_CREACION'            => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate())),
      'TITULO_UNIVERSITARIO'      => new sfWidgetFormFilterInput(),
      'FECHA_GRADO_UNIVERSITARIO' => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate())),
      'NUMERO_TARJETA'            => new sfWidgetFormFilterInput(),
      'OTROS_IDIOMAS'             => new sfWidgetFormFilterInput(),
      'UBICACION_LABORAL'         => new sfWidgetFormFilterInput(),
      'ESTADO_CIVIL'              => new sfWidgetFormFilterInput(),
      'NUMERO_HIJOS'              => new sfWidgetFormFilterInput(),
    ));

    $this->setValidators(array(
      'ESTADOCONTRATISTA_ID'      => new sfValidatorPropelChoice(array('required' => false, 'model' => 'EstadoContratista', 'column' => 'ESTADOCONTRATISTA_ID')),
      'TIPOIDENTIFICACION_ID'     => new sfValidatorPropelChoice(array('required' => false, 'model' => 'TipoIdentificacion', 'column' => 'TIPOIDENTIFICACION_ID')),
      'USUARIO_ID'                => new sfValidatorPropelChoice(array('required' => false, 'model' => 'Usuario', 'column' => 'USUARIO_ID')),
      'CIUDAD_ID'                 => new sfValidatorPropelChoice(array('required' => false, 'model' => 'Ciudad', 'column' => 'CIUDAD_ID')),
      'DEPENDENCIA_ID'            => new sfValidatorPropelChoice(array('required' => false, 'model' => 'Dependencia', 'column' => 'DEPENDENCIA_ID')),
      'CARGO_ID'                  => new sfValidatorPropelChoice(array('required' => false, 'model' => 'Cargo', 'column' => 'CARGO_ID')),
      'TIPOCONTRATO_ID'           => new sfValidatorPropelChoice(array('required' => false, 'model' => 'TipoContrato', 'column' => 'TIPOCONTRATO_ID')),
      'NUMERO_IDENTIFICACION'     => new sfValidatorPass(array('required' => false)),
      'NOMBRE'                    => new sfValidatorPass(array('required' => false)),
      'PRIMER_APELLIDO'           => new sfValidatorPass(array('required' => false)),
      'SEGUNDO_APELLIDO'          => new sfValidatorPass(array('required' => false)),
      'GENERO'                    => new sfValidatorPass(array('required' => false)),
      'NACIONALIDAD'              => new sfValidatorPass(array('required' => false)),
      'LIBRETA_MILITAR'           => new sfValidatorPass(array('required' => false)),
      'FECHA_NACIMIENTO'          => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDate(array('required' => false)), 'to_date' => new sfValidatorDate(array('required' => false)))),
      'DIRECCION_ENVIO'           => new sfValidatorPass(array('required' => false)),
      'EMAIL'                     => new sfValidatorPass(array('required' => false)),
      'EDUCACION_BASICA'          => new sfValidatorPass(array('required' => false)),
      'FECHA_GRADO_BASICA'        => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDate(array('required' => false)), 'to_date' => new sfValidatorDate(array('required' => false)))),
      'EDUCACION_SUPERIOR'        => new sfValidatorPass(array('required' => false)),
      'EXPERIENCIA_LABORAL'       => new sfValidatorPass(array('required' => false)),
      'EPS'                       => new sfValidatorPass(array('required' => false)),
      'FONDO_PENSIONES'           => new sfValidatorPass(array('required' => false)),
      'FECHA_CREACION'            => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDate(array('required' => false)), 'to_date' => new sfValidatorDate(array('required' => false)))),
      'TITULO_UNIVERSITARIO'      => new sfValidatorPass(array('required' => false)),
      'FECHA_GRADO_UNIVERSITARIO' => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDate(array('required' => false)), 'to_date' => new sfValidatorDate(array('required' => false)))),
      'NUMERO_TARJETA'            => new sfValidatorPass(array('required' => false)),
      'OTROS_IDIOMAS'             => new sfValidatorPass(array('required' => false)),
      'UBICACION_LABORAL'         => new sfValidatorPass(array('required' => false)),
      'ESTADO_CIVIL'              => new sfValidatorPass(array('required' => false)),
      'NUMERO_HIJOS'              => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
    ));

    $this->widgetSchema->setNameFormat('control_contratistas_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'ControlContratistas';
  }

  public function getFields()
  {
    return array(
      'CONTROLCONTRATISTAS_ID'    => 'Number',
      'ESTADOCONTRATISTA_ID'      => 'ForeignKey',
      'TIPOIDENTIFICACION_ID'     => 'ForeignKey',
      'USUARIO_ID'                => 'ForeignKey',
      'CIUDAD_ID'                 => 'ForeignKey',
      'DEPENDENCIA_ID'            => 'ForeignKey',
      'CARGO_ID'                  => 'ForeignKey',
      'TIPOCONTRATO_ID'           => 'ForeignKey',
      'NUMERO_IDENTIFICACION'     => 'Text',
      'NOMBRE'                    => 'Text',
      'PRIMER_APELLIDO'           => 'Text',
      'SEGUNDO_APELLIDO'          => 'Text',
      'GENERO'                    => 'Text',
      'NACIONALIDAD'              => 'Text',
      'LIBRETA_MILITAR'           => 'Text',
      'FECHA_NACIMIENTO'          => 'Date',
      'DIRECCION_ENVIO'           => 'Text',
      'EMAIL'                     => 'Text',
      'EDUCACION_BASICA'          => 'Text',
      'FECHA_GRADO_BASICA'        => 'Date',
      'EDUCACION_SUPERIOR'        => 'Text',
      'EXPERIENCIA_LABORAL'       => 'Text',
      'EPS'                       => 'Text',
      'FONDO_PENSIONES'           => 'Text',
      'FECHA_CREACION'            => 'Date',
      'TITULO_UNIVERSITARIO'      => 'Text',
      'FECHA_GRADO_UNIVERSITARIO' => 'Date',
      'NUMERO_TARJETA'            => 'Text',
      'OTROS_IDIOMAS'             => 'Text',
      'UBICACION_LABORAL'         => 'Text',
      'ESTADO_CIVIL'              => 'Text',
      'NUMERO_HIJOS'              => 'Number',
    );
  }
}
