<?php

/**
 * ClienteContenido filter form base class.
 *
 * @package    simad
 * @subpackage filter
 * @author     Your name here
 */
abstract class BaseClienteContenidoFormFilter extends BaseFormFilterPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'USUARIO_ID'                 => new sfWidgetFormPropelChoice(array('model' => 'Usuario', 'add_empty' => true)),
      'VERIFICACIONCONTCLIENTE_ID' => new sfWidgetFormPropelChoice(array('model' => 'VerificacionContcliente', 'add_empty' => true)),
      'TIPODOCUMENTAL_ID'          => new sfWidgetFormPropelChoice(array('model' => 'TipoDocumental', 'add_empty' => true)),
      'CLIENTE_ID'                 => new sfWidgetFormPropelChoice(array('model' => 'Cliente', 'add_empty' => true)),
      'CL_ESTADO_CONTENIDO_ID'     => new sfWidgetFormPropelChoice(array('model' => 'ClEstadoContenido', 'add_empty' => true)),
      'DESCRIPCION'                => new sfWidgetFormFilterInput(),
      'RUTA'                       => new sfWidgetFormFilterInput(),
      'FECHA_CREACION'             => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate())),
      'FOLIOS'                     => new sfWidgetFormFilterInput(),
      'CREADO_POR_WEB'             => new sfWidgetFormFilterInput(),
    ));

    $this->setValidators(array(
      'USUARIO_ID'                 => new sfValidatorPropelChoice(array('required' => false, 'model' => 'Usuario', 'column' => 'USUARIO_ID')),
      'VERIFICACIONCONTCLIENTE_ID' => new sfValidatorPropelChoice(array('required' => false, 'model' => 'VerificacionContcliente', 'column' => 'VERIFICACIONCONTCLIENTE_ID')),
      'TIPODOCUMENTAL_ID'          => new sfValidatorPropelChoice(array('required' => false, 'model' => 'TipoDocumental', 'column' => 'TIPODOCUMENTAL_ID')),
      'CLIENTE_ID'                 => new sfValidatorPropelChoice(array('required' => false, 'model' => 'Cliente', 'column' => 'CLIENTE_ID')),
      'CL_ESTADO_CONTENIDO_ID'     => new sfValidatorPropelChoice(array('required' => false, 'model' => 'ClEstadoContenido', 'column' => 'CL_ESTADO_CONTENIDO_ID')),
      'DESCRIPCION'                => new sfValidatorPass(array('required' => false)),
      'RUTA'                       => new sfValidatorPass(array('required' => false)),
      'FECHA_CREACION'             => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDate(array('required' => false)), 'to_date' => new sfValidatorDate(array('required' => false)))),
      'FOLIOS'                     => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'CREADO_POR_WEB'             => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
    ));

    $this->widgetSchema->setNameFormat('cliente_contenido_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'ClienteContenido';
  }

  public function getFields()
  {
    return array(
      'CLIENTE_CONTENIDO_ID'       => 'Number',
      'USUARIO_ID'                 => 'ForeignKey',
      'VERIFICACIONCONTCLIENTE_ID' => 'ForeignKey',
      'TIPODOCUMENTAL_ID'          => 'ForeignKey',
      'CLIENTE_ID'                 => 'ForeignKey',
      'CL_ESTADO_CONTENIDO_ID'     => 'ForeignKey',
      'DESCRIPCION'                => 'Text',
      'RUTA'                       => 'Text',
      'FECHA_CREACION'             => 'Date',
      'FOLIOS'                     => 'Number',
      'CREADO_POR_WEB'             => 'Number',
    );
  }
}
