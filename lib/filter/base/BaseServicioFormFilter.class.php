<?php

/**
 * Servicio filter form base class.
 *
 * @package    simad
 * @subpackage filter
 * @author     Your name here
 */
abstract class BaseServicioFormFilter extends BaseFormFilterPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'USUARIO_ID'                    => new sfWidgetFormPropelChoice(array('model' => 'Usuario', 'add_empty' => true)),
      'EMPRESA_MENSAJERIA_ID'         => new sfWidgetFormPropelChoice(array('model' => 'EmpresaMensajeria', 'add_empty' => true)),
      'PERIODO_ID'                    => new sfWidgetFormPropelChoice(array('model' => 'Periodo', 'add_empty' => true)),
      'PRIORIDADSOLICITUDSERVICIO_ID' => new sfWidgetFormPropelChoice(array('model' => 'PrioridadSolicitudServicio', 'add_empty' => true)),
      'DIRECTORIOEXTERNO_ID'          => new sfWidgetFormPropelChoice(array('model' => 'DirectorioExterno', 'add_empty' => true)),
      'TIPOSERVICIO_ID'               => new sfWidgetFormPropelChoice(array('model' => 'TipoServicio', 'add_empty' => true)),
      'REGIONAL_ID'                   => new sfWidgetFormPropelChoice(array('model' => 'Regional', 'add_empty' => true)),
      'SERVICIOESTADO_ID'             => new sfWidgetFormPropelChoice(array('model' => 'ServicioEstado', 'add_empty' => true)),
      'DETALLE'                       => new sfWidgetFormFilterInput(),
      'FECHA_CREACION'                => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate())),
      'EMAIL_DESTINO'                 => new sfWidgetFormFilterInput(),
      'FOLIOS'                        => new sfWidgetFormFilterInput(),
      'RADICADO'                      => new sfWidgetFormFilterInput(),
      'GUIA'                          => new sfWidgetFormFilterInput(),
      'FECHA_ENVIO_GUIA'              => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate())),
      'VALOR_GUIA'                    => new sfWidgetFormFilterInput(),
      'NUMERO_RADICACION'             => new sfWidgetFormFilterInput(),
      'ZONA_SERVICIO'                 => new sfWidgetFormFilterInput(),
      'SERVICIOZONA_ID'               => new sfWidgetFormFilterInput(),
      'OBS_DEVOLUCION'                => new sfWidgetFormFilterInput(),
      'MARCA'                         => new sfWidgetFormFilterInput(),
      'PREFIJO'                       => new sfWidgetFormFilterInput(),
      'FUNCIONARIO_DESTINO'           => new sfWidgetFormFilterInput(),
      'CARGO_DESTINATARIO'            => new sfWidgetFormFilterInput(),
      'DIRECCION_DESTINATARIO'        => new sfWidgetFormFilterInput(),
      'SERVICIOTIPODEVOLUCION_ID'     => new sfWidgetFormPropelChoice(array('model' => 'ServicioTipoDevolucion', 'add_empty' => true)),
      'NUMEROS_CAJA'                  => new sfWidgetFormFilterInput(),
      'NUMEROS_CARPETA'               => new sfWidgetFormFilterInput(),
    ));

    $this->setValidators(array(
      'USUARIO_ID'                    => new sfValidatorPropelChoice(array('required' => false, 'model' => 'Usuario', 'column' => 'USUARIO_ID')),
      'EMPRESA_MENSAJERIA_ID'         => new sfValidatorPropelChoice(array('required' => false, 'model' => 'EmpresaMensajeria', 'column' => 'EMPRESA_MENSAJERIA_ID')),
      'PERIODO_ID'                    => new sfValidatorPropelChoice(array('required' => false, 'model' => 'Periodo', 'column' => 'PERIODO_ID')),
      'PRIORIDADSOLICITUDSERVICIO_ID' => new sfValidatorPropelChoice(array('required' => false, 'model' => 'PrioridadSolicitudServicio', 'column' => 'PRIORIDADSOLICITUDSERVICIO_ID')),
      'DIRECTORIOEXTERNO_ID'          => new sfValidatorPropelChoice(array('required' => false, 'model' => 'DirectorioExterno', 'column' => 'DIRECTORIOEXTERNO_ID')),
      'TIPOSERVICIO_ID'               => new sfValidatorPropelChoice(array('required' => false, 'model' => 'TipoServicio', 'column' => 'TIPOSERVICIO_ID')),
      'REGIONAL_ID'                   => new sfValidatorPropelChoice(array('required' => false, 'model' => 'Regional', 'column' => 'REGIONAL_ID')),
      'SERVICIOESTADO_ID'             => new sfValidatorPropelChoice(array('required' => false, 'model' => 'ServicioEstado', 'column' => 'SERVICIOESTADO_ID')),
      'DETALLE'                       => new sfValidatorPass(array('required' => false)),
      'FECHA_CREACION'                => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDate(array('required' => false)), 'to_date' => new sfValidatorDate(array('required' => false)))),
      'EMAIL_DESTINO'                 => new sfValidatorPass(array('required' => false)),
      'FOLIOS'                        => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'RADICADO'                      => new sfValidatorPass(array('required' => false)),
      'GUIA'                          => new sfValidatorPass(array('required' => false)),
      'FECHA_ENVIO_GUIA'              => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDate(array('required' => false)), 'to_date' => new sfValidatorDate(array('required' => false)))),
      'VALOR_GUIA'                    => new sfValidatorSchemaFilter('text', new sfValidatorNumber(array('required' => false))),
      'NUMERO_RADICACION'             => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'ZONA_SERVICIO'                 => new sfValidatorPass(array('required' => false)),
      'SERVICIOZONA_ID'               => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'OBS_DEVOLUCION'                => new sfValidatorPass(array('required' => false)),
      'MARCA'                         => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'PREFIJO'                       => new sfValidatorPass(array('required' => false)),
      'FUNCIONARIO_DESTINO'           => new sfValidatorPass(array('required' => false)),
      'CARGO_DESTINATARIO'            => new sfValidatorPass(array('required' => false)),
      'DIRECCION_DESTINATARIO'        => new sfValidatorPass(array('required' => false)),
      'SERVICIOTIPODEVOLUCION_ID'     => new sfValidatorPropelChoice(array('required' => false, 'model' => 'ServicioTipoDevolucion', 'column' => 'SERVICIOTIPODEVOLUCION_ID')),
      'NUMEROS_CAJA'                  => new sfValidatorPass(array('required' => false)),
      'NUMEROS_CARPETA'               => new sfValidatorPass(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('servicio_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'Servicio';
  }

  public function getFields()
  {
    return array(
      'SERVICIO_ID'                   => 'Number',
      'USUARIO_ID'                    => 'ForeignKey',
      'EMPRESA_MENSAJERIA_ID'         => 'ForeignKey',
      'PERIODO_ID'                    => 'ForeignKey',
      'PRIORIDADSOLICITUDSERVICIO_ID' => 'ForeignKey',
      'DIRECTORIOEXTERNO_ID'          => 'ForeignKey',
      'TIPOSERVICIO_ID'               => 'ForeignKey',
      'REGIONAL_ID'                   => 'ForeignKey',
      'SERVICIOESTADO_ID'             => 'ForeignKey',
      'DETALLE'                       => 'Text',
      'FECHA_CREACION'                => 'Date',
      'EMAIL_DESTINO'                 => 'Text',
      'FOLIOS'                        => 'Number',
      'RADICADO'                      => 'Text',
      'GUIA'                          => 'Text',
      'FECHA_ENVIO_GUIA'              => 'Date',
      'VALOR_GUIA'                    => 'Number',
      'NUMERO_RADICACION'             => 'Number',
      'ZONA_SERVICIO'                 => 'Text',
      'SERVICIOZONA_ID'               => 'Number',
      'OBS_DEVOLUCION'                => 'Text',
      'MARCA'                         => 'Number',
      'PREFIJO'                       => 'Text',
      'FUNCIONARIO_DESTINO'           => 'Text',
      'CARGO_DESTINATARIO'            => 'Text',
      'DIRECCION_DESTINATARIO'        => 'Text',
      'SERVICIOTIPODEVOLUCION_ID'     => 'ForeignKey',
      'NUMEROS_CAJA'                  => 'Text',
      'NUMEROS_CARPETA'               => 'Text',
    );
  }
}
