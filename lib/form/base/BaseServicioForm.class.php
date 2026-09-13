<?php

/**
 * Servicio form base class.
 *
 * @method Servicio getObject() Returns the current form's model object
 *
 * @package    ##PROJECT_NAME##
 * @subpackage form
 * @author     ##AUTHOR_NAME##
 */
abstract class BaseServicioForm extends BaseFormPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'SERVICIO_ID'                   => new sfWidgetFormInputHidden(),
      'USUARIO_ID'                    => new sfWidgetFormPropelChoice(array('model' => 'Usuario', 'add_empty' => false)),
      'EMPRESA_MENSAJERIA_ID'         => new sfWidgetFormPropelChoice(array('model' => 'EmpresaMensajeria', 'add_empty' => true)),
      'PERIODO_ID'                    => new sfWidgetFormPropelChoice(array('model' => 'Periodo', 'add_empty' => false)),
      'PRIORIDADSOLICITUDSERVICIO_ID' => new sfWidgetFormPropelChoice(array('model' => 'PrioridadSolicitudServicio', 'add_empty' => false)),
      'DIRECTORIOEXTERNO_ID'          => new sfWidgetFormPropelChoice(array('model' => 'DirectorioExterno', 'add_empty' => true)),
      'TIPOSERVICIO_ID'               => new sfWidgetFormPropelChoice(array('model' => 'TipoServicio', 'add_empty' => false)),
      'REGIONAL_ID'                   => new sfWidgetFormPropelChoice(array('model' => 'Regional', 'add_empty' => false)),
      'SERVICIOESTADO_ID'             => new sfWidgetFormPropelChoice(array('model' => 'ServicioEstado', 'add_empty' => false)),
      'DETALLE'                       => new sfWidgetFormInputText(),
      'FECHA_CREACION'                => new sfWidgetFormDateTime(),
      'EMAIL_DESTINO'                 => new sfWidgetFormInputText(),
      'FOLIOS'                        => new sfWidgetFormInputText(),
      'RADICADO'                      => new sfWidgetFormInputText(),
      'GUIA'                          => new sfWidgetFormInputText(),
      'FECHA_ENVIO_GUIA'              => new sfWidgetFormDateTime(),
      'VALOR_GUIA'                    => new sfWidgetFormInputText(),
      'NUMERO_RADICACION'             => new sfWidgetFormInputText(),
      'ZONA_SERVICIO'                 => new sfWidgetFormInputText(),
      'SERVICIOZONA_ID'               => new sfWidgetFormInputText(),
      'OBS_DEVOLUCION'                => new sfWidgetFormInputText(),
      'MARCA'                         => new sfWidgetFormInputText(),
      'PREFIJO'                       => new sfWidgetFormInputText(),
      'FUNCIONARIO_DESTINO'           => new sfWidgetFormInputText(),
      'CARGO_DESTINATARIO'            => new sfWidgetFormInputText(),
      'DIRECCION_DESTINATARIO'        => new sfWidgetFormInputText(),
      'SERVICIOTIPODEVOLUCION_ID'     => new sfWidgetFormPropelChoice(array('model' => 'ServicioTipoDevolucion', 'add_empty' => true)),
      'NUMEROS_CAJA'                  => new sfWidgetFormInputText(),
      'NUMEROS_CARPETA'               => new sfWidgetFormInputText(),
    ));

    $this->setValidators(array(
      'SERVICIO_ID'                   => new sfValidatorChoice(array('choices' => array($this->getObject()->getServicioId()), 'empty_value' => $this->getObject()->getServicioId(), 'required' => false)),
      'USUARIO_ID'                    => new sfValidatorPropelChoice(array('model' => 'Usuario', 'column' => 'USUARIO_ID')),
      'EMPRESA_MENSAJERIA_ID'         => new sfValidatorPropelChoice(array('model' => 'EmpresaMensajeria', 'column' => 'EMPRESA_MENSAJERIA_ID', 'required' => false)),
      'PERIODO_ID'                    => new sfValidatorPropelChoice(array('model' => 'Periodo', 'column' => 'PERIODO_ID')),
      'PRIORIDADSOLICITUDSERVICIO_ID' => new sfValidatorPropelChoice(array('model' => 'PrioridadSolicitudServicio', 'column' => 'PRIORIDADSOLICITUDSERVICIO_ID')),
      'DIRECTORIOEXTERNO_ID'          => new sfValidatorPropelChoice(array('model' => 'DirectorioExterno', 'column' => 'DIRECTORIOEXTERNO_ID', 'required' => false)),
      'TIPOSERVICIO_ID'               => new sfValidatorPropelChoice(array('model' => 'TipoServicio', 'column' => 'TIPOSERVICIO_ID')),
      'REGIONAL_ID'                   => new sfValidatorPropelChoice(array('model' => 'Regional', 'column' => 'REGIONAL_ID')),
      'SERVICIOESTADO_ID'             => new sfValidatorPropelChoice(array('model' => 'ServicioEstado', 'column' => 'SERVICIOESTADO_ID')),
      'DETALLE'                       => new sfValidatorString(array('max_length' => 800, 'required' => false)),
      'FECHA_CREACION'                => new sfValidatorDateTime(array('required' => false)),
      'EMAIL_DESTINO'                 => new sfValidatorString(array('max_length' => 80, 'required' => false)),
      'FOLIOS'                        => new sfValidatorInteger(array('min' => -2147483648, 'max' => 2147483647, 'required' => false)),
      'RADICADO'                      => new sfValidatorString(array('max_length' => 80, 'required' => false)),
      'GUIA'                          => new sfValidatorString(array('max_length' => 80, 'required' => false)),
      'FECHA_ENVIO_GUIA'              => new sfValidatorDateTime(array('required' => false)),
      'VALOR_GUIA'                    => new sfValidatorNumber(array('required' => false)),
      'NUMERO_RADICACION'             => new sfValidatorInteger(array('min' => -2147483648, 'max' => 2147483647, 'required' => false)),
      'ZONA_SERVICIO'                 => new sfValidatorString(array('max_length' => 100, 'required' => false)),
      'SERVICIOZONA_ID'               => new sfValidatorInteger(array('min' => -2147483648, 'max' => 2147483647, 'required' => false)),
      'OBS_DEVOLUCION'                => new sfValidatorString(array('max_length' => 500, 'required' => false)),
      'MARCA'                         => new sfValidatorInteger(array('min' => -2147483648, 'max' => 2147483647, 'required' => false)),
      'PREFIJO'                       => new sfValidatorString(array('max_length' => 20, 'required' => false)),
      'FUNCIONARIO_DESTINO'           => new sfValidatorString(array('max_length' => 500, 'required' => false)),
      'CARGO_DESTINATARIO'            => new sfValidatorString(array('max_length' => 500, 'required' => false)),
      'DIRECCION_DESTINATARIO'        => new sfValidatorString(array('max_length' => 500, 'required' => false)),
      'SERVICIOTIPODEVOLUCION_ID'     => new sfValidatorPropelChoice(array('model' => 'ServicioTipoDevolucion', 'column' => 'SERVICIOTIPODEVOLUCION_ID', 'required' => false)),
      'NUMEROS_CAJA'                  => new sfValidatorString(array('max_length' => 500, 'required' => false)),
      'NUMEROS_CARPETA'               => new sfValidatorString(array('max_length' => 500, 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('servicio[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'Servicio';
  }


}
